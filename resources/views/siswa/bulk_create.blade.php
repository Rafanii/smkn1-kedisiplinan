@extends('layouts.app')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#4f46e5',
                    slate: { 800: '#1e293b', 900: '#0f172a' }
                }
            }
        },
        corePlugins: { preflight: false }
    }
</script>

<div class="page-container p-4">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 pb-1 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Banyak Siswa Sekaligus</h1>
            <p class="text-sm text-gray-500 mt-1">Tambahkan multiple siswa ke satu kelas dengan mudah dan cepat.</p>
        </div>
        
        <div class="flex flex-wrap gap-2 mt-3 sm:mt-0">
            <a href="{{ route('siswa.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2 no-underline">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('siswa.create') }}" class="px-4 py-2 bg-blue-500 text-white text-sm font-bold rounded-xl hover:bg-blue-600 shadow-lg shadow-blue-200 transition-all flex items-center gap-2 no-underline">
                <i class="fas fa-user-plus"></i> Tambah Satuan
            </a>
        </div>
    </div>

    <form action="{{ route('siswa.bulk-store') }}" method="POST" enctype="multipart/form-data" id="bulkCreateForm">
        @csrf

        {{-- Alert Error --}}
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('bulk_errors'))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-4 rounded-r">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-amber-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700 font-bold mb-2">Beberapa baris bermasalah:</p>
                        <ul class="list-disc list-inside text-xs text-amber-600 space-y-1">
                            @foreach(session('bulk_errors') as $be)
                                <li>{{ $be }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Card 1: Pilih Kelas --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-700 m-0 uppercase tracking-wide flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            1. Pilih Kelas Tujuan
                        </h3>
                    </div>
                    <div class="p-6">
                        <label class="form-label-modern">Kelas <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="kelas_id" class="form-input-modern w-full appearance-none pr-8" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach(App\Models\Kelas::orderBy('nama_kelas')->get() as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Input Data Siswa --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-700 m-0 uppercase tracking-wide flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            2. Input Data Siswa
                        </h3>
                    </div>
                    <div class="p-6">
                        
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 mb-4 text-xs text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Tips:</strong> Copy-paste dari Excel/Sheets langsung ke tabel, atau upload file CSV/XLSX.
                        </div>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <button type="button" id="addRowBtn" class="px-4 py-2 bg-indigo-500 text-white text-sm font-bold rounded-lg hover:bg-indigo-600 transition flex items-center gap-2">
                                <i class="fas fa-plus"></i> Tambah Baris
                            </button>
                            <button type="button" id="pasteHintBtn" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-200 transition flex items-center gap-2">
                                <i class="fas fa-clipboard"></i> Paste dari Spreadsheet
                            </button>
                        </div>

                        <div class="overflow-x-auto border border-slate-200 rounded-xl">
                            <table class="w-full text-sm" id="bulkTable">
                                <thead class="bg-slate-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wide border-b border-slate-200" style="min-width:140px">NISN</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wide border-b border-slate-200" style="min-width:200px">Nama Lengkap</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wide border-b border-slate-200" style="min-width:140px">No. HP Wali</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-600 uppercase tracking-wide border-b border-slate-200" style="width:80px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @php $initial = old('bulk_rows') ?? 5; @endphp
                                    @for($i = 0; $i < $initial; $i++)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-2">
                                            <input type="text" class="form-input-table bulk-nisn" value="" placeholder="123456...">
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="text" class="form-input-table bulk-nama" value="" placeholder="Nama Siswa">
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="text" class="form-input-table bulk-hp" value="" placeholder="08123...">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button type="button" class="remove-row text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <p class="text-xs text-slate-500 mt-3">
                            <i class="fas fa-lightbulb text-amber-500 mr-1"></i>
                            Copy data dari Excel/Sheets dan paste ke kolom NISN pertama. Sistem akan otomatis mem-parse data.
                        </p>
                        <textarea name="bulk_data" id="bulk_data" class="hidden">{{ old('bulk_data') }}</textarea>
                    </div>
                </div>

                {{-- Card 3: Upload File --}}
                <div class="bg-white rounded-2xl shadow-sm border border-blue-200 overflow-hidden">
                    <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                        <h3 class="text-sm font-bold text-blue-800 m-0 uppercase tracking-wide flex items-center gap-2">
                            <i class="fas fa-file-upload text-blue-600"></i>
                            Alternatif: Upload File
                        </h3>
                    </div>
                    <div class="p-6">
                        <label class="form-label-modern text-blue-700">File CSV atau XLSX</label>
                        <input type="file" name="bulk_file" accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
                               class="form-input-modern w-full file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="text-xs text-slate-500 mt-2">
                            <i class="fas fa-file-excel text-emerald-500 mr-1"></i>
                            Format: <strong>NISN</strong>, <strong>Nama Lengkap</strong>, <strong>NomorHP</strong> (opsional). Jika ada file, data tabel akan diabaikan.
                        </p>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Card: Opsi Akun Wali --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-700 m-0 uppercase tracking-wide flex items-center gap-2">
                            <i class="fas fa-user-friends text-amber-500"></i>
                            Opsi Akun Wali
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-gradient-to-r from-slate-50 to-blue-50 p-4 rounded-xl border border-slate-200">
                            <div class="flex items-start">
                                <div class="flex items-center h-6">
                                    <input type="checkbox" class="w-5 h-5 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 focus:ring-2 cursor-pointer transition" 
                                           id="create_wali_all" name="create_wali_all" value="1">
                                </div>
                                <label class="ml-3 cursor-pointer" for="create_wali_all">
                                    <span class="text-sm font-bold text-slate-800 block">Buat akun Wali Murid otomatis</span>
                                    <span class="text-xs text-slate-500 mt-0.5 block">Sistem akan membuat akun untuk setiap siswa yang ditambahkan</span>
                                </label>
                            </div>
                            
                            <div id="bulk-preview" class="mt-4 p-3 bg-white border border-indigo-200 rounded-lg hidden">
                                <p class="text-xs font-bold text-indigo-800 mb-2">
                                    <i class="fas fa-eye mr-1"></i> Preview Username:
                                </p>
                                <code id="bulk-preview-sample" class="text-xs text-indigo-600 bg-indigo-50 px-3 py-1 rounded block">-</code>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Action Buttons --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
                    <div class="p-6">
                        <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-3 flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500"></i> Proses Data
                        </h4>
                        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                            Pastikan kelas tujuan dan data siswa sudah benar sebelum memproses.
                        </p>
                        
                        <button type="submit" id="bulkSubmitBtn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-indigo-200 transition-all transform active:scale-95 mb-3 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Proses Tambah Banyak
                        </button>
                        
                        <a href="{{ route('siswa.index') }}" class="w-full block text-center bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold py-3 px-4 rounded-xl transition-colors text-sm">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- Card: Panduan --}}
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-5">
                    <h5 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <i class="fas fa-book text-amber-500"></i> Panduan
                    </h5>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                            <span>Pilih kelas tujuan terlebih dahulu</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                            <span>Isi data siswa di tabel atau upload file</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                            <span>NISN minimal 8 digit numeric</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                            <span>Nomor HP opsional (untuk notifikasi)</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </form>
</div>

@endsection

@section('styles')
<style>
    .form-label-modern {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.5rem;
        letter-spacing: 0.025em;
    }

    .form-input-modern {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        line-height: 1.25;
        color: #1e293b;
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-input-modern:focus {
        border-color: #6366f1;
        outline: 0;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .form-input-table {
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #1e293b;
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-input-table:focus {
        border-color: #6366f1;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-input-table::placeholder {
        color: #cbd5e1;
        font-size: 0.8rem;
    }
</style>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/siswa/bulk_create.js') }}"></script>
@endpush