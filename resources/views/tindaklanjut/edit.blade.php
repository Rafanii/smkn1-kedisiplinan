@extends('layouts.app')

@section('content')

{{-- 1. TAILWIND CONFIG - Samakan persis dengan halaman Input Pelanggaran --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#0f172a',
                    indigo: { 600: '#4f46e5', 50: '#eef2ff', 100: '#e0e7ff', 700: '#4338ca' },
                    emerald: { 50: '#ecfdf5', 100: '#d1fae5', 600: '#059669', 700: '#047857' },
                    rose: { 50: '#fff1f2', 100: '#ffe4e6', 600: '#e11d48', 700: '#be123c' }
                }
            }
        },
        corePlugins: { preflight: false }
    }
</script>

<style>
    .page-wrap-custom { font-family: 'Inter', sans-serif; }
    .btn-clean-action {
        padding: 0.5rem 1rem; border-radius: 0.75rem; background: #fff; color: #475569; 
        font-size: 0.75rem; font-weight: 700; border: 1px solid #e2e8f0; transition: 0.2s;
    }
    .btn-clean-action:hover { background: #f1f5f9; color: #0f172a; }
</style>

<div class="page-wrap-custom min-h-screen p-5 bg-slate-50">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 pb-1 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">Manajemen Kasus</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 m-0 tracking-tight flex items-center gap-3">
                    <i class="fas fa-tasks text-indigo-600"></i> Kelola Kasus: {{ $kasus->siswa->nama_siswa }}
                </h1>
            </div>
            
            <a href="javascript:history.back()" class="btn-clean-action no-underline">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <div class="lg:col-span-5 space-y-5">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-14 h-14 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xl font-black shadow-md">
                                {{ strtoupper(substr($kasus->siswa->nama_siswa, 0, 1)) }}
                            </div>
                            <div>
                                <h2 class="text-base font-black text-slate-800 leading-tight mb-1">{{ $kasus->siswa->nama_siswa }}</h2>
                                <div class="flex gap-2 text-[10px] font-bold uppercase tracking-wider">
                                    <span class="text-slate-400">NISN: {{ $kasus->siswa->nisn }}</span>
                                    <span class="text-indigo-600 px-1.5 py-0.5 bg-indigo-50 rounded border border-indigo-100">{{ $kasus->siswa->kelas->nama_kelas }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">Pemicu Kasus</label>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 italic text-xs text-slate-600 leading-relaxed font-medium">
                                    "{{ $kasus->pemicu }}"
                                </div>
                            </div>

                            <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 flex justify-between items-center">
                                <span class="text-[9px] font-black text-rose-400 uppercase tracking-widest">Sanksi Sistem</span>
                                <span class="text-[11px] font-black text-rose-700">{{ $kasus->sanksi_deskripsi }}</span>
                            </div>

                            
                        </div>
                    </div>
                </div>
             </div>

            {{-- SURAT PANGGILAN MANAGEMENT SECTION --}}
            @if($kasus->suratPanggilan)
            <div class="lg:col-span-12">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-emerald-50/50 border-b border-emerald-100">
                        <h3 class="text-xs font-black uppercase tracking-widest text-emerald-700 m-0 flex items-center gap-2">
                            <i class="fas fa-envelope"></i> Surat Panggilan
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        {{-- Informasi Surat --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Nomor Surat</span>
                                <div class="font-mono text-sm text-slate-700 font-bold">{{ $kasus->suratPanggilan->nomor_surat }}</div>
                            </div>
                            <div>
                                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tipe Surat</span>
                                <span class="px-3 py-1 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200">
                                    {{ $kasus->suratPanggilan->tipe_surat }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tanggal Pertemuan</span>
                                <div class="text-sm text-slate-700 font-semibold">
                                    <i class="far fa-calendar text-slate-400"></i> 
                                    {{ \Carbon\Carbon::parse($kasus->suratPanggilan->tanggal_pertemuan)->format('d M Y') }}
                                    <span class="ml-2 text-slate-400">•</span>
                                    <i class="far fa-clock text-slate-400 ml-2"></i>
                                    {{ $kasus->suratPanggilan->waktu_pertemuan }}
                                </div>
                            </div>
                            <div>
                                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status Cetak</span>
                                @if($kasus->suratPanggilan->printLogs->count() > 0)
                                    <span class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">
                                        <i class="fas fa-check-double"></i> {{ $kasus->suratPanggilan->printLogs->count() }}x dicetak
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                        <i class="fas fa-times"></i> Belum dicetak
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Riwayat Cetak --}}
                        @if($kasus->suratPanggilan->printLogs->count() > 0)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-history text-slate-400"></i>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Riwayat Cetak</span>
                            </div>
                            <div class="space-y-2">
                                @foreach($kasus->suratPanggilan->printLogs->take(3) as $log)
                                <div class="flex items-center gap-3 text-xs">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-[10px]"></i>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-bold text-slate-700">{{ $log->user->nama ?? $log->user->username }}</span>
                                        <span class="text-slate-400 mx-2">•</span>
                                        <span class="text-slate-500">{{ $log->printed_at->diffForHumans() }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $log->printed_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @endforeach
                            </div>
                            @if($kasus->suratPanggilan->printLogs->count() > 3)
                            <div class="text-center mt-3 pt-3 border-t border-slate-200">
                                <span class="text-[10px] text-slate-400 font-bold">+ {{ $kasus->suratPanggilan->printLogs->count() - 3 }} cetak lainnya</span>
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Action Buttons - Preview & Cetak Only --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Preview Button --}}
                            <a href="{{ route('tindak-lanjut.preview-surat', $kasus->id) }}" 
                               class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-blue-50 text-blue-700 border-2 border-blue-200 hover:bg-blue-100 hover:border-blue-300 transition-all font-bold text-xs uppercase tracking-wider no-underline group">
                                <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                                <span>Preview Surat</span>
                            </a>
                            
                            {{-- Cetak Button --}}
                            <a href="{{ route('tindak-lanjut.cetak-surat', $kasus->id) }}" 
                               onclick="return confirm('Cetak surat untuk {{ $kasus->siswa->nama_siswa }}?')"
                               target="_blank"
                               class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 border-2 border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 transition-all font-bold text-xs uppercase tracking-wider no-underline group">
                                <i class="fas fa-print group-hover:scale-110 transition-transform"></i>
                                <span>Cetak Surat</span>
                            </a>
                        </div>

                        {{-- Status Management --}}
                        @if($kasus->status->value === 'Baru')
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200">
                            <div class="flex items-start gap-3">
                                <div class="mt-1">
                                    <i class="fas fa-info-circle text-indigo-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-xs font-bold text-indigo-900 mb-1">Mulai Tangani Kasus</div>
                                    <div class="text-[11px] text-indigo-700 mb-3 leading-relaxed">
                                        Status saat ini: <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-bold">Baru</span>
                                        <br>Klik tombol dibawah untuk mengubah status menjadi "Sedang Ditangani"
                                    </div>
                                    <form action="{{ route('tindak-lanjut.mulai-tangani', $kasus->id) }}" method="POST" 
                                          onsubmit="return confirm('Mulai menangani kasus ini?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-lg shadow-indigo-200">
                                            <i class="fas fa-play-circle"></i> Mulai Tangani Kasus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @endif
            {{-- END SURAT PANGGILAN SECTION --}}

            <div class="lg:col-span-7">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-500 m-0">Hasil Penanganan / Tindak Lanjut</h3>
                    </div>

                    <form action="{{ route('kasus.update', $kasus->id) }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        @method('PUT')

                        @if($kasus->status == 'Selesai')
                            <div class="p-5 bg-emerald-50 border border-emerald-100 rounded-xl text-center mb-4">
                                <div class="text-emerald-600 font-black text-xs uppercase tracking-widest mb-1">✅ KASUS DITUTUP</div>
                                <p class="text-[11px] text-emerald-700 m-0">Dinyatakan selesai pada: {{ \Carbon\Carbon::parse($kasus->tanggal_tindak_lanjut)->format('d F Y') }}</p>
                            </div>
                            
                            <div class="opacity-60 pointer-events-none">
                                <textarea class="w-full p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs italic" rows="3" disabled>{{ $kasus->denda_deskripsi }}</textarea>
                            </div>
                            <div class="mt-4">
                                <a href="javascript:history.back()" class="btn-clean-action no-underline inline-block">Kembali</a>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Tanggal Penanganan</label>
                                    <input type="date" name="tanggal_tindak_lanjut" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 outline-none focus:border-indigo-500" value="{{ $kasus->tanggal_tindak_lanjut ? \Carbon\Carbon::parse($kasus->tanggal_tindak_lanjut)->format('Y-m-d') : date('Y-m-d') }}" required>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Status Kasus</label>
                                    @if(Auth::user()->hasRole('Kepala Sekolah'))
                                        <div class="p-2.5 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center gap-2">
                                            <input type="checkbox" name="status" value="Disetujui" class="w-4 h-4 rounded" required>
                                            <span class="text-[10px] font-black text-indigo-700 uppercase">Setujui Sanksi</span>
                                        </div>
                                    @else
                                        @if($kasus->status == 'Menunggu Persetujuan')
                                            <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-bold text-slate-400 italic">🔒 Terkunci: Menunggu Kepsek</div>
                                            <input type="hidden" name="status" value="Menunggu Persetujuan">
                                        @else
                                            <select name="status" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 outline-none focus:border-indigo-500" required>
                                                @if($kasus->status == 'Baru') <option value="Baru" selected>BARU</option> @endif
                                                @if($kasus->status == 'Disetujui' || $kasus->status == 'Ditangani')
                                                    <option value="Disetujui" disabled>✅ TELAH DISETUJUI</option>
                                                @endif
                                                <option value="Ditangani" {{ $kasus->status == 'Ditangani' ? 'selected' : '' }}>SEDANG DITANGANI</option>
                                                <option value="Selesai">SELESAI (TUTUP KASUS)</option>
                                            </select>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Catatan / Denda Tambahan</label>
                                <textarea name="denda_deskripsi" rows="3" class="w-full p-4 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-indigo-500 shadow-inner" placeholder="Contoh: Siswa diminta membawa bibit tanaman...">{{ $kasus->denda_deskripsi }}</textarea>
                            </div>

                            <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                                Simpan Perubahan <i class="fas fa-save ml-1"></i>
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection