@extends('layouts.app')

@section('title', 'Dashboard Waka Sarana')

@section('content')
<div class="container-fluid">

    <!-- WELCOME MESSAGE -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="callout callout-warning shadow-sm border-left-warning">
                <h5><i class="fas fa-tools mr-2 text-warning"></i> Selamat Datang, {{ Auth::user()->nama }}!</h5>
                <p class="text-muted mb-0">Anda berada di Panel Waka Sarana. Fokus pada pelanggaran fasilitas sekolah.</p>
            </div>
        </div>
    </div>

    <!-- STATISTIK PELANGGARAN FASILITAS -->
    <h5 class="mb-3 text-dark font-weight-bold"><i class="fas fa-chart-bar text-warning mr-2"></i> Statistik Pelanggaran Fasilitas</h5>
    
    <div class="row">
        <!-- Total Pelanggaran Fasilitas -->
        <div class="col-lg-6 col-12">
            <div class="small-box bg-warning shadow">
                <div class="inner">
                    <h3>{{ $totalPelanggaranFasilitas }}</h3>
                    <p>Total Pelanggaran Fasilitas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <!-- Pelanggaran Bulan Ini -->
        <div class="col-lg-6 col-12">
            <div class="small-box bg-danger shadow">
                <div class="inner">
                    <h3>{{ $pelanggaranBulanIni }}</h3>
                    <p>Pelanggaran Bulan Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- AKSI CEPAT -->
    <h5 class="mb-3 text-dark font-weight-bold"><i class="fas fa-rocket text-primary mr-2"></i> Aksi Cepat</h5>
    
    <div class="row mb-4">
        <div class="col-md-6 col-sm-12 mb-3">
            <a href="{{ route('pelanggaran.create') }}" class="btn btn-app bg-white shadow-sm btn-block text-left pl-3 border">
                <span class="badge bg-danger">Catat</span>
                <i class="fas fa-plus-circle text-danger" style="font-size: 2rem; float: right;"></i>
                <strong>Catat Pelanggaran</strong><br>
                Tambah pelanggaran baru
            </a>
        </div>
        
        <div class="col-md-6 col-sm-12 mb-3">
            <a href="{{ route('my-riwayat.index') }}" class="btn btn-app bg-white shadow-sm btn-block text-left pl-3 border">
                <i class="fas fa-history text-info" style="font-size: 2rem; float: right;"></i>
                <strong>Riwayat Saya</strong><br>
                Lihat & kelola riwayat yang saya catat
            </a>
        </div>
    </div>

    <div class="row">
        
        <!-- RIWAYAT PELANGGARAN FASILITAS TERBARU -->
        <div class="col-lg-7 col-md-12 mb-4">
            <div class="card card-outline card-warning h-100 shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-list text-warning mr-1"></i> Riwayat Pelanggaran Fasilitas Terbaru
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-warning">{{ $riwayatTerbaru->count() }} Records</span>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0">
                    @if($riwayatTerbaru->isEmpty())
                        <div class="text-center p-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3" style="opacity: 0.5;"></i>
                            <h5>Tidak ada pelanggaran fasilitas.</h5>
                            <p class="text-muted small">Fasilitas sekolah dalam kondisi baik.</p>
                        </div>
                    @else
                        <table class="table table-hover table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Pelanggaran</th>
                                    <th class="text-center">Poin</th>
                                    <th>Pencatat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatTerbaru as $r)
                                <tr>
                                    <td class="text-muted small">{{ $r->tanggal_kejadian->format('d/m/Y') }}</td>
                                    <td><span class="font-weight-bold">{{ $r->siswa->nama_siswa }}</span></td>
                                    <td class="text-muted small">{{ $r->siswa->kelas->nama_kelas }}</td>
                                    <td>
                                        <span class="d-block text-sm">{{ $r->jenisPelanggaran->nama_pelanggaran }}</span>
                                        @if($r->keterangan)
                                            <small class="text-muted">{{ Str::limit($r->keterangan, 30) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-danger">+{{ $r->poin_diberikan }}</span>
                                    </td>
                                    <td class="text-muted small">{{ $r->guruPencatat->nama ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIWAYAT YANG SAYA CATAT -->
        <div class="col-lg-5 col-md-12">
            <div class="card card-outline card-info h-100 shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-user-edit text-info mr-1"></i> Riwayat yang Saya Catat
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">{{ $riwayatSaya->count() }} Records</span>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0">
                    @if($riwayatSaya->isEmpty())
                        <div class="text-center p-5">
                            <i class="fas fa-clipboard-list fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                            <h5>Belum ada riwayat.</h5>
                            <p class="text-muted small">Anda belum mencatat pelanggaran.</p>
                        </div>
                    @else
                        <table class="table table-hover table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Pelanggaran</th>
                                    <th class="text-center">Poin</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatSaya as $r)
                                <tr>
                                    <td class="text-muted small">{{ $r->tanggal_kejadian->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="font-weight-bold">{{ $r->siswa->nama_siswa }}</span><br>
                                        <small class="text-muted">{{ $r->siswa->kelas->nama_kelas }}</small>
                                    </td>
                                    <td>
                                        <span class="d-block text-sm">{{ Str::limit($r->jenisPelanggaran->nama_pelanggaran, 25) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-danger">+{{ $r->poin_diberikan }}</span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('my-riwayat.edit', $r->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer text-center">
                            <a href="{{ route('my-riwayat.index') }}" class="btn btn-sm btn-info">
                                <i class="fas fa-list mr-1"></i> Lihat Semua Riwayat Saya
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
