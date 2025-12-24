<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TindakLanjut;
use App\Models\RiwayatPelanggaran;
use App\Models\Siswa;

class KepsekDashboardController extends Controller
{
    public function index(Request $request)
    {
        // FILTER (Default: Bulan Ini)
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // KASUS SURAT (Clean & Informatif)
        // Kepala Sekolah: Tampilkan SEMUA kasus yang melibatkan dia
        // Biasanya kasus yang memerlukan approval/mengetahui
        $kasusBaru = TindakLanjut::with(['siswa.kelas', 'suratPanggilan'])
            ->forPembina('Kepala Sekolah')  // Filter: Hanya yang melibatkan Kepsek
            ->whereHas('suratPanggilan')  // Filter: Harus punya surat
            ->whereIn('status', ['Baru', 'Menunggu Persetujuan', 'Disetujui', 'Ditangani'])
            ->latest()
            ->get();

        // Kasus Menunggu Approval
        $kasusMenunggu = TindakLanjut::with(['siswa.kelas', 'suratPanggilan'])
            ->forPembina('Kepala Sekolah')
            ->where('status', 'Menunggu Persetujuan')
            ->latest()
            ->get();

        // DIAGRAM: Pelanggaran Populer (SEMUA SISWA DI SEKOLAH)
        $chartPelanggaran = DB::table('riwayat_pelanggaran')
            ->join('jenis_pelanggaran', 'riwayat_pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.id')
            ->whereDate('riwayat_pelanggaran.tanggal_kejadian', '>=', $startDate)
            ->whereDate('riwayat_pelanggaran.tanggal_kejadian', '<=', $endDate)
            ->select('jenis_pelanggaran.nama_pelanggaran', DB::raw('count(*) as total'))
            ->groupBy('jenis_pelanggaran.nama_pelanggaran')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $chartLabels = $chartPelanggaran->pluck('nama_pelanggaran');
        $chartData = $chartPelanggaran->pluck('total');

        // DIAGRAM 2: Pelanggaran Per Jurusan
        $chartJurusan = DB::table('riwayat_pelanggaran')
            ->join('siswa', 'riwayat_pelanggaran.siswa_id', '=', 'siswa.id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('jurusan', 'kelas.jurusan_id', '=', 'jurusan.id')
            ->whereDate('riwayat_pelanggaran.tanggal_kejadian', '>=', $startDate)
            ->whereDate('riwayat_pelanggaran.tanggal_kejadian', '<=', $endDate)
            ->select('jurusan.nama_jurusan', DB::raw('count(*) as total'))
            ->groupBy('jurusan.nama_jurusan')
            ->orderByDesc('total')
            ->get();

        $chartJurusanLabels = $chartJurusan->pluck('nama_jurusan');
        $chartJurusanData = $chartJurusan->pluck('total');

        // DIAGRAM 3: Trend Pelanggaran Bulanan (6 bulan terakhir)
        $chartTrend = DB::table('riwayat_pelanggaran')
            ->select(
                DB::raw("DATE_FORMAT(tanggal_kejadian, '%Y-%m') as bulan"),
                DB::raw('count(*) as total')
            )
            ->where('tanggal_kejadian', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        $chartTrendLabels = $chartTrend->pluck('bulan');
        $chartTrendData = $chartTrend->pluck('total');

        // STATISTIK
        $totalSiswa = Siswa::count();
        $totalKasus = $kasusBaru->count();
        $totalKasusMenunggu = $kasusMenunggu->count();
        $totalPelanggaran = RiwayatPelanggaran::whereDate('tanggal_kejadian', '>=', $startDate)
            ->whereDate('tanggal_kejadian', '<=', $endDate)
            ->count();

        return view('dashboards.kepsek', compact(
            'kasusBaru',
            'kasusMenunggu',
            'chartLabels', 
            'chartData',
            'chartJurusanLabels',
            'chartJurusanData',
            'chartTrendLabels',
            'chartTrendData',
            'totalSiswa',
            'totalKasus',
            'totalKasusMenunggu',
            'totalPelanggaran',
            'startDate', 
            'endDate'
        ));
    }
}
