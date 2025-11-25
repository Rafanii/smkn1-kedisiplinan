@extends('layouts.app')

@section('title', 'Tinjau Kasus - Persetujuan')

@section('content')
<div class="container-fluid">
    
    <!-- Header & Navigation -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="text-dark font-weight-bold">
                    <i class="fas fa-file-signature mr-2"></i> Tinjau & Setujui Kasus
                </h3>
                <a href="{{ route('kepala-sekolah.approvals.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Detail Kasus (Main Content) -->
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white font-weight-bold">
                        Identitas Siswa & Pelanggaran
                    </h3>
                </div>

                <div class="card-body">
                    <!-- Identitas Siswa -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-dark">Nama Siswa</h6>
                            <p class="text-primary font-weight-bold text-lg">{{ $kasus->siswa->nama_siswa }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-dark">NISN</h6>
                            <p class="font-weight-bold text-lg">{{ $kasus->siswa->nisn }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-dark">Kelas</h6>
                            <p class="badge badge-info p-2">{{ $kasus->siswa->kelas->nama_kelas ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-dark">Jurusan</h6>
                            <p class="badge badge-secondary p-2">{{ $kasus->siswa->kelas->jurusan->nama_jurusan ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Detail Pelanggaran -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-dark">Deskripsi Pelanggaran</h6>
                            <div class="p-3 bg-light border rounded">
                                <p class="mb-0">{{ $kasus->pemicu }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-dark">Tanggal Kejadian</h6>
                            <p>{{ \Carbon\Carbon::parse($kasus->tanggal_kejadian ?? now())->format('d M Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-dark">Dilaporkan Oleh</h6>
                            <p>{{ $kasus->user->nama ?? 'Unknown' }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Rekomendasi Sanksi -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-dark">Rekomendasi Sanksi</h6>
                            <div class="p-3 bg-danger bg-opacity-10 border-left border-4 border-danger rounded">
                                <p class="font-weight-bold text-danger mb-0">{{ $kasus->sanksi_deskripsi ?? 'Belum ditentukan' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Surat Panggilan -->
                    @if($kasus->suratPanggilan)
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-dark">
                                <i class="fas fa-file-pdf mr-2 text-danger"></i> Surat Panggilan
                            </h6>
                            <div class="p-3 bg-light rounded">
                                <p><strong>Nomor Surat:</strong> {{ $kasus->suratPanggilan->nomor_surat ?? 'N/A' }}</p>
                                <p><strong>Tanggal Surat:</strong> {{ $kasus->suratPanggilan->tanggal_surat ? \Carbon\Carbon::parse($kasus->suratPanggilan->tanggal_surat)->format('d M Y') : 'N/A' }}</p>
                                <a href="{{ route('kasus.cetak', $kasus->id) }}" class="btn btn-danger btn-sm" target="_blank">
                                    <i class="fas fa-download mr-1"></i> Unduh Surat
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Approval (Sidebar) -->
        <div class="col-lg-4">
            <div class="card card-outline card-success shadow-sm">
                <div class="card-header bg-success">
                    <h3 class="card-title text-white font-weight-bold">
                        <i class="fas fa-check-circle mr-2"></i> Keputusan
                    </h3>
                </div>

                <form action="{{ route('kepala-sekolah.approvals.process', $kasus->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <!-- Catatan -->
                        <div class="form-group">
                            <label for="catatan" class="font-weight-bold">Catatan / Alasan (Opsional)</label>
                            <textarea class="form-control form-control-sm" id="catatan" name="catatan_kepala_sekolah" rows="4" placeholder="Tuliskan catatan atau alasan keputusan Anda..."></textarea>
                            <small class="form-text text-muted">Catatan akan dicatat dalam arsip.</small>
                        </div>

                        <!-- Approval Buttons -->
                        <div class="form-group">
                            <label class="font-weight-bold">Keputusan Anda:</label>
                            <div class="btn-group btn-group-toggle w-100" role="group">
                                <label class="btn btn-outline-danger w-50">
                                    <input type="radio" name="action" id="reject" value="reject"> 
                                    <i class="fas fa-times-circle mr-1"></i> Tolak
                                </label>
                                <label class="btn btn-outline-success w-50 active">
                                    <input type="radio" name="action" id="approve" value="approve" checked> 
                                    <i class="fas fa-check-circle mr-1"></i> Setujui
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim Keputusan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="card card-outline card-info mt-3">
                <div class="card-header bg-info">
                    <h6 class="card-title text-white font-weight-bold">
                        <i class="fas fa-info-circle mr-2"></i> Informasi
                    </h6>
                </div>
                <div class="card-body p-3 text-sm">
                    <p><strong>Status Saat Ini:</strong> <span class="badge badge-warning">{{ $kasus->status }}</span></p>
                    <p><strong>Dibuat Pada:</strong> {{ $kasus->created_at->format('d M Y H:i') }}</p>
                    <p class="mb-0 text-muted"><em>Keputusan Anda akan disimpan dalam sistem dan diberitahukan kepada pihak terkait.</em></p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
