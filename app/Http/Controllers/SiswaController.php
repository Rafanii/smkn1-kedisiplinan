<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    /**
     * MENAMPILKAN DAFTAR SISWA
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $allJurusan = Jurusan::all();
        $allKelas = Kelas::orderBy('nama_kelas')->get();

        $query = Siswa::with('kelas.jurusan');

        // --- LOGIKA DATA SCOPING ---
        if ($user->hasRole('Wali Kelas')) {
            $kelasBinaan = $user->kelasDiampu;
            if ($kelasBinaan) {
                $query->where('kelas_id', $kelasBinaan->id);
            } else {
                $query->where('id', 0); 
            }
        }
        elseif ($user->hasRole('Kaprodi')) {
            $jurusanBinaan = $user->jurusanDiampu;
            if ($jurusanBinaan) {
                $query->whereHas('kelas', function($q) use ($jurusanBinaan) {
                    $q->where('jurusan_id', $jurusanBinaan->id);
                });
            } else {
                $query->where('id', 0);
            }
        }

        // --- LOGIKA FILTER ---
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->cari . '%')
                  ->orWhere('nisn', 'like', '%' . $request->cari . '%');
            });
        }

        if (!$user->hasRole('Wali Kelas') && $request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if (!$user->hasAnyRole(['Wali Kelas', 'Kaprodi']) && $request->filled('jurusan_id')) {
             $query->whereHas('kelas', function($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }

        if ($request->filled('tingkat')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('nama_kelas', 'like', $request->tingkat . ' %');
            });
        }

        $siswa = $query->orderBy('kelas_id')->orderBy('nama_siswa')
                       ->paginate(20)->withQueryString();

        return view('siswa.index', compact('siswa', 'allJurusan', 'allKelas'));
    }

    /**
     * TAMPILKAN FORM TAMBAH SISWA
     */
    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        
        // [UPDATE] Ambil daftar user dengan role 'Wali Murid' untuk dropdown
        $waliMurid = User::whereHas('role', function($q){
            $q->where('nama_role', 'Wali Murid');
        })->orderBy('nama')->get();

        return view('siswa.create', compact('kelas', 'waliMurid'));
    }

    /**
     * SIMPAN DATA SISWA BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|numeric|unique:siswa,nisn',
            'nama_siswa' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'nomor_hp_wali_murid' => 'nullable|numeric',
            // [UPDATE] Validasi opsional untuk wali murid
            'wali_murid_user_id' => 'nullable|exists:users,id',
        ]);

        Siswa::create($request->all());

        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Ditambahkan');
    }

    /**
     * TAMPILKAN FORM EDIT
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        
        $waliMurid = User::whereHas('role', function($q){
            $q->where('nama_role', 'Wali Murid');
        })->orderBy('nama')->get();

        return view('siswa.edit', compact('siswa', 'kelas', 'waliMurid'));
    }

    /**
     * UPDATE DATA SISWA
     */
    public function update(Request $request, Siswa $siswa)
    {
        $user = Auth::user();

        // Jika Wali Kelas, Validasi lebih longgar (Cuma HP)
            if ($user->hasRole('Wali Kelas')) {
            // Pastikan Wali Kelas hanya dapat mengubah siswa di kelas yang dia ampuh
            $kelasBinaan = Auth::user()->kelasDiampu;
            if (!$kelasBinaan || $siswa->kelas_id !== $kelasBinaan->id) {
                abort(403, 'AKSES DITOLAK: Anda hanya dapat memperbarui data siswa di kelas yang Anda ampu.');
            }
            $request->validate([
                'nomor_hp_wali_murid' => 'nullable|numeric',
            ]);

            $siswa->update([
                'nomor_hp_wali_murid' => $request->nomor_hp_wali_murid
            ]);
        } 
        // Jika Operator, Validasi Ketat
        else {
            $request->validate([
                'nisn' => 'required|numeric|unique:siswa,nisn,' . $siswa->id,
                'nama_siswa' => 'required|string|max:255',
                'kelas_id' => 'required|exists:kelas,id',
                'nomor_hp_wali_murid' => 'nullable|numeric',
                'wali_murid_user_id' => 'nullable|exists:users,id',
            ]);

            // Map incoming request keys if older forms still submit old names
            $data = $request->all();
            if ($request->filled('orang_tua_user_id') && !$request->filled('wali_murid_user_id')) {
                $data['wali_murid_user_id'] = $request->input('orang_tua_user_id');
            }
            if ($request->filled('nomor_hp_ortu') && !$request->filled('nomor_hp_wali_murid')) {
                $data['nomor_hp_wali_murid'] = $request->input('nomor_hp_ortu');
            }

            $siswa->update($data);
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * HAPUS SISWA
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Dihapus');
    }
}