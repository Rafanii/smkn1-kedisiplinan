<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    /**
     * Show audit form (GET /audit/siswa)
     */
    public function show()
    {
        $allKelas = Kelas::orderBy('nama_kelas')->get();
        $allJurusan = Jurusan::orderBy('nama_jurusan')->get();

        return view('audit.siswa.index', compact('allKelas', 'allJurusan'));
    }

    /**
     * Preview (dry-run) delete impact (POST /audit/siswa/preview)
     */
    public function preview(Request $request)
    {
        $request->validate([
            'scope' => 'required|in:kelas,jurusan,tingkat',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tingkat' => 'nullable|string|max:2',
        ]);

        $query = Siswa::query();
        $scopeName = '';

        if ($request->scope === 'kelas' && $request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
            $kelas = Kelas::find($request->kelas_id);
            $scopeName = $kelas ? "Kelas {$kelas->nama_kelas}" : 'Unknown Kelas';
        } elseif ($request->scope === 'jurusan' && $request->jurusan_id) {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
            $jurusan = Jurusan::find($request->jurusan_id);
            $scopeName = $jurusan ? "Jurusan {$jurusan->nama_jurusan}" : 'Unknown Jurusan';
        } elseif ($request->scope === 'tingkat' && $request->tingkat) {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('nama_kelas', 'like', "{$request->tingkat}%");
            });
            $scopeName = "Tingkat {$request->tingkat}";
        }

        $siswas = $query->get();
        $totalSiswa = $siswas->count();

        if ($totalSiswa === 0) {
            return redirect()->back()->with('warning', 'Tidak ada siswa yang sesuai dengan kriteria.');
        }

        // Count related records
        $siswaIds = $siswas->pluck('id');

        $totalRiwayat = DB::table('riwayat_pelanggaran')
            ->whereIn('siswa_id', $siswaIds)
            ->count();

        $totalTindak = DB::table('tindak_lanjut')
            ->whereIn('siswa_id', $siswaIds)
            ->count();

        $totalSurat = DB::table('surat_panggilan')
            ->join('tindak_lanjut', 'surat_panggilan.tindak_lanjut_id', '=', 'tindak_lanjut.id')
            ->whereIn('tindak_lanjut.siswa_id', $siswaIds)
            ->count();

        $totalWali = DB::table('siswa')
            ->whereIn('id', $siswaIds)
            ->whereNotNull('wali_murid_user_id')
            ->distinct()
            ->count('wali_murid_user_id');

        // Flash untuk reuse di form confirm
        session([
            'audit_scope' => $request->scope,
            'audit_kelas_id' => $request->kelas_id,
            'audit_jurusan_id' => $request->jurusan_id,
            'audit_tingkat' => $request->tingkat,
            'audit_siswas' => $siswas->toArray(),
        ]);

        return view('audit.siswa.summary', [
            'scopeName' => $scopeName,
            'totalSiswa' => $totalSiswa,
            'totalRiwayat' => $totalRiwayat,
            'totalTindak' => $totalTindak,
            'totalSurat' => $totalSurat,
            'totalWali' => $totalWali,
            'siswas' => $siswas,
        ]);
    }

    /**
     * Export backup CSV (GET /audit/siswa/export)
     */
    public function export(Request $request)
    {
        $siswas = collect(session('audit_siswas', []));
        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Session audit tidak valid. Silakan jalankan preview terlebih dahulu.');
        }

        $siswaIds = $siswas->pluck('id');

        $filename = 'siswa_backup_' . now()->format('Ymd_His') . '.csv';

        $csv = "=== BACKUP PENGHAPUSAN SISWA ===\n";
        $csv .= "Tanggal: " . now()->format('Y-m-d H:i:s') . "\n";
        $csv .= "Operator: " . Auth::user()?->nama . "\n";
        $csv .= "Total Siswa: " . $siswas->count() . "\n\n";

        $csv .= "--- DATA SISWA ---\n";
        $csv .= "ID,NISN,Nama,Kelas,Wali Murid,No HP,Created At\n";
        foreach ($siswas as $siswa) {
            $kelas = DB::table('kelas')->where('id', $siswa['kelas_id'])->first();
            $kelasNama = $kelas?->nama_kelas ?? 'N/A';
            $csv .= "{$siswa['id']},{$siswa['nisn']},{$siswa['nama_siswa']},{$kelasNama},N/A,{$siswa['nomor_hp_wali_murid']},{$siswa['created_at']}\n";
        }

        return response()->streamDownload(
            fn () => print($csv),
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]
        );
    }

    /**
     * Perform delete (DELETE /audit/siswa via POST /audit/siswa/destroy)
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|in:DELETE',
            'delete_orphaned_wali' => 'nullable|boolean',
        ]);

        $siswas = collect(session('audit_siswas', []));
        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Session audit tidak valid. Silakan jalankan preview terlebih dahulu.');
        }

        $isForceDelete = $request->input('force_delete', false);
        $deleteOrphanedWali = $request->input('delete_orphaned_wali', false);

        try {
            DB::beginTransaction();

            $siswaIds = $siswas->pluck('id');
            $deleteMethod = $isForceDelete ? 'forceDelete' : 'delete';

            // Get wali IDs before deletion
            $waliIds = DB::table('siswa')
                ->whereIn('id', $siswaIds)
                ->whereNotNull('wali_murid_user_id')
                ->distinct()
                ->pluck('wali_murid_user_id')
                ->toArray();

            // Delete siswa
            foreach ($siswaIds as $siswaId) {
                $siswa = Siswa::withTrashed()->find($siswaId);
                if ($siswa) {
                    $siswa->{$deleteMethod}();
                }
            }

            // Delete orphaned wali accounts if user confirmed
            $deletedWaliCount = 0;
            if ($deleteOrphanedWali && !empty($waliIds)) {
                foreach ($waliIds as $waliId) {
                    $countOtherSiswa = DB::table('siswa')
                        ->where('wali_murid_user_id', $waliId)
                        ->where('deleted_at', null) // Only count non-deleted
                        ->count();

                    if ($countOtherSiswa === 0) {
                        // Orphaned - delete this wali account
                        $user = User::find($waliId);
                        if ($user) {
                            $user->delete(); // Soft-delete user account
                            $deletedWaliCount++;
                        }
                    }
                }
            }

            DB::commit();

            // Clear session
            session()->forget(['audit_scope', 'audit_kelas_id', 'audit_jurusan_id', 'audit_tingkat', 'audit_siswas']);

            $message = "✓ Penghapusan selesai! {$siswas->count()} siswa telah dihapus (" . 
                ($isForceDelete ? 'permanent' : 'soft-delete, dapat di-restore') . ").";
            
            if ($deletedWaliCount > 0) {
                $message .= " {$deletedWaliCount} akun Wali Murid orphaned juga dihapus.";
            }

            return redirect()->route('siswa.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Terjadi kesalahan: {$e->getMessage()}");
        }
    }

    /**
     * Show confirmation delete page
     */
    public function confirmDelete()
    {
        $siswas = collect(session('audit_siswas', []));
        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Session audit tidak valid.');
        }

        $siswaIds = $siswas->pluck('id');

        $totalRiwayat = DB::table('riwayat_pelanggaran')->whereIn('siswa_id', $siswaIds)->count();
        $totalTindak = DB::table('tindak_lanjut')->whereIn('siswa_id', $siswaIds)->count();
        $totalSurat = DB::table('surat_panggilan')
            ->join('tindak_lanjut', 'surat_panggilan.tindak_lanjut_id', '=', 'tindak_lanjut.id')
            ->whereIn('tindak_lanjut.siswa_id', $siswaIds)
            ->count();

        // Get all wali accounts related to these students
        $waliIds = DB::table('siswa')
            ->whereIn('id', $siswaIds)
            ->whereNotNull('wali_murid_user_id')
            ->distinct()
            ->pluck('wali_murid_user_id')
            ->toArray();

        // Check which wali are now orphaned (no other siswa relations after this delete)
        $orphanedWaliIds = [];
        foreach ($waliIds as $waliId) {
            $countOtherSiswa = DB::table('siswa')
                ->where('wali_murid_user_id', $waliId)
                ->whereNotIn('id', $siswaIds) // Exclude the ones we're deleting
                ->where('deleted_at', null) // Only count non-deleted
                ->count();

            if ($countOtherSiswa === 0) {
                $orphanedWaliIds[] = $waliId;
            }
        }

        // Get wali account details
        $orphanedWalis = [];
        if (!empty($orphanedWaliIds)) {
            $orphanedWalis = DB::table('users')
                ->whereIn('id', $orphanedWaliIds)
                ->select('id', 'nama', 'username', 'email')
                ->get();
        }

        return view('audit.siswa.confirm-delete', [
            'siswas' => $siswas,
            'totalRiwayat' => $totalRiwayat,
            'totalTindak' => $totalTindak,
            'totalSurat' => $totalSurat,
            'orphanedWalis' => $orphanedWalis,
            'orphanedWaliIds' => $orphanedWaliIds,
        ]);
    }
}
