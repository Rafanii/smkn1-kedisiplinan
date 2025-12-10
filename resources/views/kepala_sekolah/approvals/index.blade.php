@extends('layouts.app')

@section('title', 'Persetujuan & Validasi Kasus')

@section('content')
<div class="container-fluid">
    
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="text-dark font-weight-bold">
                    <i class="fas fa-file-signature mr-2"></i> Persetujuan Kasus
                </h3>
                <a href="{{ route('dashboard.kepsek') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Summary -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>{{ $kasusMenunggu->total() }} Kasus Menunggu Persetujuan</strong>
                — Tinjau dan berikan keputusan untuk setiap kasus.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>

    <!-- List Kasus Menunggu -->
    <div class="row">
        <div class="col-12">
            @if($kasusMenunggu->isEmpty())
                <div class="card card-outline card-success">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                        <h5>Tidak Ada Kasus yang Menunggu</h5>
                        <p class="text-muted">Semua kasus telah diproses.</p>
                    </div>
                </div>
            @else
                <div class="card card-outline card-danger">
                    <div class="card-header bg-danger">
                        <h3 class="card-title text-white font-weight-bold">
                            <i class="fas fa-bell mr-2"></i> Daftar Kasus
                        </h3>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Rekomendasi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kasusMenunggu as $kasus)
                                <tr>
                                    <td>
                                        <small class="font-weight-bold">{{ $kasus->created_at->format('d M Y') }}</small><br>
                                        <small class="text-muted">{{ $kasus->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 font-weight-bold">{{ $kasus->siswa->nama_siswa }}</h6>
                                        <small class="text-muted">NISN: {{ $kasus->siswa->nisn }}</small>
                                    </td>
                                    <td>
                                        <small class="badge badge-info">{{ $kasus->siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($kasus->pemicu, 30) }}</small>
                                    </td>
                                    <td>
                                        <div class="p-2 bg-light border-left border-danger rounded font-weight-bold text-sm">
                                            {{ $kasus->sanksi_deskripsi ?? 'Belum ditentukan' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('kepala-sekolah.approvals.show', $kasus->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye mr-1"></i> Tinjau
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer">
                        {{ $kasusMenunggu->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
