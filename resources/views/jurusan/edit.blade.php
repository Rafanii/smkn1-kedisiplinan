@extends('layouts.app')

@section('title', 'Edit Jurusan')

@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h3>Edit Jurusan</h3>
        </div>
    </div>

    <form action="{{ route('jurusan.update', $jurusan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Jurusan</label>
                    <input type="text" name="nama_jurusan" class="form-control" value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}" required>
                </div>

                <div class="form-group">
                    <label>Kode Jurusan (editable)</label>
                    <input type="text" name="kode_jurusan" class="form-control" value="{{ old('kode_jurusan', $jurusan->kode_jurusan) }}">
                </div>

                <div class="form-group">
                    <label>Kaprodi saat ini</label>
                    <div>
                        @if($jurusan->kaprodi)
                            <strong>{{ $jurusan->kaprodi->nama }}</strong> ({{ $jurusan->kaprodi->username }})
                            <p class="text-muted">Jika Anda mengubah kode jurusan, username Kaprodi akan ikut diperbarui otomatis.</p>
                        @else
                            <em>Belum ada Kaprodi yang ditetapkan.</em>
                        @endif
                    </div>
                </div>

                @if(!$jurusan->kaprodi)
                <div class="form-group form-check mt-2">
                    <input type="checkbox" class="form-check-input" id="create_kaprodi" name="create_kaprodi" value="1">
                    <label class="form-check-label" for="create_kaprodi">Buat akun Kaprodi otomatis (jika belum ada)</label>
                </div>

                <div id="kaprodi_preview" class="border rounded p-2 mb-2" style="display:none;">
                    <strong>Preview akun Kaprodi (akan dibuat otomatis jika dikonfirmasi):</strong>
                    <p>Username: <span id="kaprodi_username_preview" class="font-weight-bold"></span></p>
                    <p>Password (sampel): <span id="kaprodi_password_preview" class="font-weight-bold"></span></p>
                </div>
                @endif

                <div class="form-group mt-3">
                    <button class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('jurusan.show', $jurusan) }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const chk = document.getElementById('create_kaprodi');
    if(!chk) return;
    const namaInput = document.querySelector('input[name="nama_jurusan"]');
    const kodeInput = document.querySelector('input[name="kode_jurusan"]');
    const previewBox = document.getElementById('kaprodi_preview');
    const userPreview = document.getElementById('kaprodi_username_preview');
    const passPreview = document.getElementById('kaprodi_password_preview');

    function generateKodeFromNama(nama){
        const parts = nama.trim().split(/\s+/).filter(Boolean);
        let letters = '';
        for(let p of parts){ letters += p[0].toUpperCase(); if(letters.length>=3) break; }
        return letters || 'JRS';
    }

    function normalizeKaprodiUsernameFromKode(kode){
        return 'kaprodi.' + kode.toLowerCase().replace(/[^a-z0-9]+/g, '');
    }

    function randomPassword(len=10){
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        let out = '';
        for(let i=0;i<len;i++) out += chars.charAt(Math.floor(Math.random()*chars.length));
        return out;
    }

    function updatePreview(){
        const nama = namaInput.value || '';
        let kode = kodeInput.value || '';
        if (!kode) kode = generateKodeFromNama(nama);
        const username = normalizeKaprodiUsernameFromKode(kode);
        userPreview.textContent = username;
        passPreview.textContent = randomPassword(8);
    }

    chk.addEventListener('change', function(){
        if(chk.checked){ previewBox.style.display = 'block'; updatePreview(); }
        else previewBox.style.display = 'none';
    });

    namaInput.addEventListener('input', function(){ if(chk.checked) updatePreview(); });
    kodeInput.addEventListener('input', function(){ if(chk.checked) updatePreview(); });
});
</script>
@endpush
