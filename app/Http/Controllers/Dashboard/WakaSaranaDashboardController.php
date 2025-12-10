<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPelanggaran;
use App\Models\JenisPelanggaran;
use Illuminate\Support\Facades\Auth;

class WakaSaranaDashboardController extends Controller
{
    /**
     * Display dashboard untuk Waka Sarana.
     * Fokus pada pelanggaran fasilitas.
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil jenis pelanggaran "Merusak Fasilitas"
        $jenisFasilitas = JenisPelanggaran::where('nama_pelanggaran', 'LIKE', '%merusak%fasilitas%')
            ->orWhere('nama_pelanggaran', 'LIKE', '%mencoret%')
            ->pluck('id');

        // Statistik pelanggaran fasilitas
        $totalPelanggaranFasilitas = RiwayatPelanggaran::whereIn('jenis_pelanggaran_id', $jenisFasilitas)
            ->count();

        $pelanggaranBulanIni = RiwayatPelanggaran::whereIn('jenis_pelanggaran_id', $jenisFasilitas)
            ->whereMonth('tanggal_kejadian', now()->month)
            ->whereYear('tanggal_kejadian', now()->year)
            ->count();

        // Riwayat pelanggaran fasilitas terbaru (10 records)
        $riwayatTerbaru = RiwayatPelanggaran::with(['siswa.kelas.jurusan', 'jenisPelanggaran', 'guruPencatat'])
            ->whereIn('jenis_pelanggaran_id', $jenisFasilitas)
            ->orderBy('tanggal_kejadian', 'desc')
            ->limit(10)
            ->get();

        // Riwayat yang dicatat oleh Waka Sarana sendiri (5 records)
        $riwayatSaya = RiwayatPelanggaran::with(['siswa.kelas.jurusan', 'jenisPelanggaran'])
            ->where('guru_pencatat_user_id', $user->id)
            ->orderBy('tanggal_kejadian', 'desc')
            ->limit(5)
            ->get();

        return view('dashboards.waka_sarana', compact(
            'totalPelanggaranFasilitas',
            'pelanggaranBulanIni',
            'riwayatTerbaru',
            'riwayatSaya'
        ));
    }
}
