@extends('layouts.app')

@section('title', 'Tambah Jenis Pelanggaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle mr-2"></i> Tambah Jenis Pelanggaran Baru</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Workflow:</strong>
                        <ol class="mb-0 pl-3">
                            <li>Isi form ini untuk membuat jenis pelanggaran baru</li>
                            <li>Setelah disimpan, Anda akan diarahkan ke halaman <strong>Kelola Rules</strong></li>
                            <li>Di halaman Kelola Rules, atur: <strong>Frekuensi, Poin, Sanksi, Trigger Surat, Pembina</strong></li>
                        </ol>
                    </div>

                    <form action="{{ route('jenis-pelanggaran.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="nama_pelanggaran">Nama Pelanggaran <span class="text-danger">*</span></label>
                            <input type="text" id="nama_pelanggaran" name="nama_pelanggaran" 
                                   class="form-control @error('nama_pelanggaran') is-invalid @enderror" 
                                   placeholder="Contoh: Rambut tidak sesuai (3-2-1, diwarnai, crop)" 
                                   required value="{{ old('nama_pelanggaran') }}" autofocus>
                            @error('nama_pelanggaran') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="kategori_id">Kategori Pelanggaran <span class="text-danger">*</span></label>
                            <select id="kategori_id" name="kategori_id" 
                                    class="form-control @error('kategori_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $k)
                                    <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="filter_category">Filter Kategori <small class="text-muted">(opsional)</small></label>
                            <select id="filter_category" name="filter_category" 
                                    class="form-control @error('filter_category') is-invalid @enderror">
                                <option value="">-- Tidak ada filter --</option>
                                <option value="atribut" {{ old('filter_category') == 'atribut' ? 'selected' : '' }}>Atribut/Seragam</option>
                                <option value="absensi" {{ old('filter_category') == 'absensi' ? 'selected' : '' }}>Absensi/Kehadiran</option>
                                <option value="kerapian" {{ old('filter_category') == 'kerapian' ? 'selected' : '' }}>Kerapian/Kebersihan</option>
                                <option value="ibadah" {{ old('filter_category') == 'ibadah' ? 'selected' : '' }}>Ibadah/Agama</option>
                                <option value="berat" {{ old('filter_category') == 'berat' ? 'selected' : '' }}>Berat/Kejahatan</option>
                            </select>
                            <small class="text-muted">Filter untuk memudahkan pencarian saat catat pelanggaran.</small>
                            @error('filter_category') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="keywords">Alias / Keywords <small class="text-muted">(opsional)</small></label>
                            <textarea id="keywords" name="keywords" 
                                      class="form-control @error('keywords') is-invalid @enderror" 
                                      rows="2" 
                                      placeholder="Contoh: Rambut panjang, Rambut gondrong, Rambut dicat">{{ old('keywords') }}</textarea>
                            <small class="text-muted">
                                Kata kunci alternatif untuk memudahkan pencarian. Pisahkan dengan koma atau enter.
                            </small>
                            @error('keywords') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <a href="{{ route('frequency-rules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan & Lanjut ke Kelola Rules
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
