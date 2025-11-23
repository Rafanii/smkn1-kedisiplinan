@extends('layouts.app')

@section('title', 'Master Jenis Pelanggaran')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/jenis_pelanggaran/index.css') }}">
@endsection

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 text-dark font-weight-bold">
            <i class="fas fa-list-check mr-2 text-primary"></i> Master Jenis Pelanggaran
        </h4>
        <div class="btn-group">
            <a href="{{ route('dashboard.admin') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <a href="{{ route('jenis-pelanggaran.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Aturan Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover table-striped text-nowrap">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Nama Pelanggaran</th>
                        <th>Kategori</th>
                        <th style="width: 10%; text-align: center;">Poin</th>
                        <th style="width: 15%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisPelanggaran as $key => $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->nama_pelanggaran }}</strong></td>
                        <td>
                            <span class="badge badge-info">{{ $item->kategoriPelanggaran->nama_kategori ?? '-' }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-warning">{{ $item->poin }} Poin</span>
                        </td>
                        <td style="text-align: center;">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('jenis-pelanggaran.edit', $item->id) }}" class="btn btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('jenis-pelanggaran.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i><br>
                            Belum ada data jenis pelanggaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/jenis_pelanggaran/index.js') }}"></script>
@endpush
