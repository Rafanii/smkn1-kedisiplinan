@extends('layouts.app')

@section('title', 'Tambah Jenis Pelanggaran')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/jenis_pelanggaran/create.css') }}">
@endsection

@section('content')
<div class="form-wrapper">
    <div class="form-section">
        <h5><i class="fas fa-plus-circle mr-2 text-primary"></i> Tambah Jenis Pelanggaran Baru</h5>
        
        <form action="{{ route('jenis-pelanggaran.store') }}" method="POST">
            @csrf
            
            <div class="form-group required">
                <label for="nama_pelanggaran">Nama Pelanggaran</label>
                <input type="text" id="nama_pelanggaran" name="nama_pelanggaran" class="form-control @error('nama_pelanggaran') is-invalid @enderror" 
                       placeholder="Misal: Tidur di kelas" required value="{{ old('nama_pelanggaran') }}">
                @error('nama_pelanggaran') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group required">
                <label for="kategori_id">Kategori Pelanggaran</label>
                <select id="kategori_id" name="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group required">
                <label for="poin">Bobot Poin</label>
                <input type="number" id="poin" name="poin" class="form-control @error('poin') is-invalid @enderror" 
                       placeholder="Misal: 5" min="0" required value="{{ old('poin') }}">
                <small class="text-muted">Semakin tinggi angka = semakin berat pelanggaran</small>
                @error('poin') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-actions">
                <a href="{{ route('jenis-pelanggaran.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/jenis_pelanggaran/create.js') }}"></script>
@endpush
