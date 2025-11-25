@extends('layouts.app')

@section('title', 'Detail Jurusan')

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-8">
            <h3>Detail Jurusan: {{ $jurusan->nama_jurusan }}</h3>
        </div>
        <div class="col-4 text-right">
            <a href="{{ route('jurusan.edit', $jurusan) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Kode Jurusan:</strong> {{ $jurusan->kode_jurusan ?? '-' }}</p>
            <p><strong>Kaprodi:</strong> {{ $jurusan->kaprodi?->nama ?? '-' }}</p>
            <p><strong>Jumlah Kelas:</strong> {{ $jurusan->kelas->count() }}</p>
            <p><strong>Jumlah Siswa:</strong> {{ $jurusan->siswa()->count() }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Kelas (Jurusan ini)</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurusan->kelas as $k)
                        <tr>
                            <td>{{ $k->nama_kelas }}</td>
                            <td>{{ $k->waliKelas?->nama ?? '-' }}</td>
                            <td>{{ $k->siswa()->count() }}</td>
                            <td>
                                <a href="{{ route('kelas.edit', $k) }}" class="btn btn-sm btn-warning">Edit</a>
                                <a href="{{ route('kelas.show', $k) }}" class="btn btn-sm btn-info">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Belum ada kelas untuk jurusan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
