@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-8">
            <h3>Detail Kelas: {{ $kelas->nama_kelas }}</h3>
            <p><strong>Jurusan:</strong> {{ $kelas->jurusan?->nama_jurusan ?? '-' }} ({{ $kelas->jurusan?->kode_jurusan ?? '-' }})</p>
            <p><strong>Tingkat:</strong> {{ $kelas->tingkat ?? '-' }}</p>
            <p><strong>Wali Kelas:</strong> {{ $kelas->waliKelas?->nama ?? '-' }}</p>
            <p><strong>Jumlah Siswa:</strong> {{ $kelas->siswa()->count() }}</p>
        </div>
        <div class="col-4 text-right">
            <a href="{{ route('kelas.edit', $kelas) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Siswa (Kelas ini)</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Wali Murid</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas->siswa as $s)
                        <tr>
                            <td>{{ $s->nisn ?? '-' }}</td>
                            <td>{{ $s->nama_siswa ?? '-' }}</td>
                            <td>{{ $s->waliMurid?->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Belum ada siswa di kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
