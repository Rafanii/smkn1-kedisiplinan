@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h3>Tambah Kelas</h3>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('kelas.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Tingkat</label>
                    <select name="tingkat" class="form-control" required>
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="X" {{ old('tingkat') == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ old('tingkat') == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ old('tingkat') == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                    <small class="form-text text-muted">Nama kelas akan dibuat otomatis berdasarkan tingkat dan jurusan.</small>
                </div>
                <div class="form-group">
                    <label>Jurusan</label>
                    <select name="jurusan_id" class="form-control" required>
                        <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusanList as $j)
                                <option value="{{ $j->id }}" data-kode="{{ $j->kode_jurusan ?? '' }}">{{ $j->nama_jurusan }}</option>
                            @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Wali Kelas (opsional)</label>
                    <select name="wali_kelas_user_id" class="form-control">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($waliList as $w)
                            <option value="{{ $w->id }}">{{ $w->nama }} ({{ $w->username }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group form-check mt-2">
                    <input type="checkbox" class="form-check-input" id="create_wali" name="create_wali" value="1">
                    <label class="form-check-label" for="create_wali">Buat akun Wali Kelas otomatis untuk kelas ini</label>
                </div>

                <div id="wali_preview" class="border rounded p-2 mb-2" style="display:none;">
                    <strong>Preview akun Wali Kelas (akan dibuat otomatis jika dikonfirmasi):</strong>
                    <p>Username: <span id="wali_username_preview" class="font-weight-bold"></span></p>
                    <p>Password (sampel): <span id="wali_password_preview" class="font-weight-bold"></span></p>
                </div>
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const chk = document.getElementById('create_wali');
    const tingkatEl = document.querySelector('select[name="tingkat"]');
    const jurusanEl = document.querySelector('select[name="jurusan_id"]');
    const previewBox = document.getElementById('wali_preview');
    const userPreview = document.getElementById('wali_username_preview');
    const passPreview = document.getElementById('wali_password_preview');

    function generateKodeFromNama(nama){
        const parts = nama.trim().split(/\s+/).filter(Boolean);
        let letters = '';
        for(let p of parts){ letters += p[0].toUpperCase(); if(letters.length>=3) break; }
        return letters || 'JRS';
    }

    function normalizeUsername(str){
        return str.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
    }

    function randomPassword(len=10){
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        let out = '';
        for(let i=0;i<len;i++) out += chars.charAt(Math.floor(Math.random()*chars.length));
        return out;
    }

    function updatePreview(){
        const tingkat = tingkatEl.value || '';
        const jurusanOpt = jurusanEl.selectedOptions[0];
        const jurusanKode = jurusanOpt ? jurusanOpt.dataset.kode : '';
        let kode = jurusanKode || generateKodeFromNama(jurusanOpt ? jurusanOpt.textContent : '');
        const username = normalizeUsername(kode + '_' + tingkat + '_wali');
        userPreview.textContent = username;
        passPreview.textContent = randomPassword(8);
    }

    chk.addEventListener('change', function(){
        if(chk.checked){ previewBox.style.display = 'block'; updatePreview(); }
        else previewBox.style.display = 'none';
    });

    tingkatEl.addEventListener('change', function(){ if(chk.checked) updatePreview(); });
    jurusanEl.addEventListener('change', function(){ if(chk.checked) updatePreview(); });
});
</script>
@endpush

</div>

@endsection
