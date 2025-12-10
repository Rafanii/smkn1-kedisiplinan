@extends('layouts.app')

@section('title', 'Laporan & Ekspor Data')

@section('content')
<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="text-dark font-weight-bold">
                    <i class="fas fa-file-excel mr-2"></i> Laporan & Ekspor
                </h3>
                <a href="{{ route('dashboard.kepsek') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white font-weight-bold">
                        <i class="fas fa-filter mr-2"></i> Filter & Buat Laporan
                    </h3>
                </div>

                <form action="{{ route('kepala-sekolah.reports.preview') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <!-- Report Type -->
                        <div class="form-group">
                            <label for="report_type" class="font-weight-bold">Jenis Laporan</label>
                            <select id="report_type" name="report_type" class="form-control form-control-sm" required>
                                <option value="">-- Pilih Jenis Laporan --</option>
                                <option value="pelanggaran">Laporan Pelanggaran</option>
                                <option value="siswa">Laporan Siswa Bermasalah</option>
                                <option value="tindakan">Laporan Tindakan Lanjut</option>
                            </select>
                        </div>

                        <div class="row">
                            <!-- Jurusan Filter -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jurusan_id" class="font-weight-bold">Jurusan (Opsional)</label>
                                    <select id="jurusan_id" name="jurusan_id" class="form-control form-control-sm">
                                        <option value="">-- Semua Jurusan --</option>
                                        @foreach($jurusans as $j)
                                            <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Kelas Filter -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kelas_id" class="font-weight-bold">Kelas (Opsional)</label>
                                    <select id="kelas_id" name="kelas_id" class="form-control form-control-sm">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Periode -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="periode_mulai" class="font-weight-bold">Dari Tanggal</label>
                                    <input type="date" id="periode_mulai" name="periode_mulai" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="periode_akhir" class="font-weight-bold">Sampai Tanggal</label>
                                    <input type="date" id="periode_akhir" name="periode_akhir" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                            <i class="fas fa-search mr-1"></i> Pratinjau Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-body p-3 text-sm">
                    <i class="fas fa-info-circle mr-2 text-info"></i>
                    <strong>Panduan:</strong> Isi filter sesuai kebutuhan, lalu klik "Pratinjau Laporan" untuk melihat data sebelum mengexport.
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-body p-3 text-sm">
                    <i class="fas fa-calendar-alt mr-2 text-warning"></i>
                    <strong>Export Format:</strong> CSV (kompatibel dengan Excel) atau PDF (memerlukan konfigurasi).
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
