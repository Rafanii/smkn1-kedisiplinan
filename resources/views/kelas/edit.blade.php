@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h3>Edit Kelas</h3>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('kelas.update', $kelas) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="form-control" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required>
                </div>
                <div class="form-group">
                    <label>Tingkat</label>
                    <select name="tingkat" class="form-control" required>
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="X" {{ old('tingkat', $kelas->tingkat) == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ old('tingkat', $kelas->tingkat) == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ old('tingkat', $kelas->tingkat) == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jurusan</label>
                    <select name="jurusan_id" class="form-control" required>
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ $kelas->jurusan_id == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Wali Kelas (opsional)</label>
                    <select name="wali_kelas_user_id" class="form-control">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($waliList as $w)
                            <option value="{{ $w->id }}" {{ $kelas->wali_kelas_user_id == $w->id ? 'selected' : '' }}>{{ $w->nama }} ({{ $w->username }})</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>

</div>

@endsection
