@extends('layouts.app')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: { primary: '#4f46e5', slate: { 800: '#1e293b', 900: '#0f172a' } },
                screens: { 'xs': '375px' }
            }
        },
        corePlugins: { preflight: false }
    }
</script>

<div class="page-wrap">

    <!-- Header Section -->
    <div class="relative rounded-2xl bg-gradient-to-r from-slate-800 to-blue-900 p-6 shadow-lg mb-8 text-white flex flex-col md:flex-row items-center justify-between gap-4 border border-blue-800/50 overflow-hidden">
        
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500 opacity-10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-cyan-400 opacity-10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none"></div>

        <div class="relative z-10 w-full md:w-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-medium text-blue-200 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Waka Kesiswaan Panel
            </div>
            <h1 class="text-xl md:text-2xl font-bold leading-tight">
                Halo, {{ auth()->user()->username }}! 👋
            </h1>
            <p class="text-blue-100 text-xs md:text-sm opacity-80 mt-1">
                Dashboard khusus untuk monitoring surat panggilan dan kasus siswa seluruh sekolah.
            </p>
        </div>

        <div class="hidden xs:flex items-center gap-3 bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 shadow-inner min-w-[140px] relative z-10">
            <div class="bg-blue-500/20 p-2 rounded-lg text-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <div>
                <span class="block text-2xl font-bold leading-none tracking-tight">{{ date('d') }}</span>
                <span class="block text-[10px] uppercase tracking-wider text-blue-200">{{ date('F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-6">
        <form method="GET" action="{{ route('dashboard.admin') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Jurusan</label>
                <select name="jurusan_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    <option value="">-- Semua --</option>
                    @foreach($allJurusan as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->kode_jurusan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Kelas</label>
                <select name="kelas_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    <option value="">-- Semua --</option>
                    @foreach($allKelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        
        <div class="stat-card hover-lift border-l-4 border-l-emerald-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Populasi</span>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalSiswa }}</h3>
            <p class="text-xs text-slate-500">Total Siswa Sekolah</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card hover-lift border-l-4 border-l-rose-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kasus Surat</span>
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300 {{ $totalKasus > 0 ? 'animate-pulse' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $totalKasus }}</h3>
            <p class="text-xs text-slate-500">Yang Melibatkan Saya</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-rose-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card hover-lift border-l-4 border-l-amber-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pelanggaran</span>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $pelanggaranFiltered }}</h3>
            <p class="text-xs text-slate-500">Total Periode Ini</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-amber-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

        <div class="stat-card hover-lift border-l-4 border-l-blue-500 group relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kasus Aktif</span>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-1">{{ $kasusAktif }}</h3>
            <p class="text-xs text-slate-500">Belum Selesai</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </div>

    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Kasus Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                <h3 class="text-sm font-bold text-slate-700 m-0">📋 Kasus Surat Panggilan</h3>
                @if($totalKasus > 0)
                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded border border-rose-100">
                        {{ $totalKasus }} Kasus
                    </span>
                @endif
            </div>
            
            <div class="overflow-x-auto max-h-[500px]">
                <table class="float-table text-left w-full">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold tracking-wider sticky top-0">
                        <tr>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3">Pemicu</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($daftarKasus as $kasus)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="text-sm font-bold text-slate-800">{{ $kasus->siswa->nama_siswa }}</div>
                                <div class="text-[10px] text-slate-500">{{ $kasus->siswa->kelas->nama_kelas ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-slate-600 truncate max-w-[120px]" title="{{ $kasus->pemicu }}">
                                    {{ Str::limit($kasus->pemicu, 25) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($kasus->status == 'Baru')
                                    <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded">Baru</span>
                                @elseif($kasus->status == 'Menunggu Persetujuan')
                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-1 rounded">Menunggu</span>
                                @else
                                    <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ $kasus->status }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('kasus.edit', $kasus->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-white bg-emerald-600 px-2 py-1 rounded hover:bg-emerald-700 shadow-sm transition no-underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-slate-400 text-xs">
                                Tidak ada kasus surat yang melibatkan Waka Kesiswaan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Chart 1: Pelanggaran Populer -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/30">
                    <h3 class="text-sm font-bold text-slate-700 m-0">📊 Pelanggaran Populer</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Top 10 pelanggaran</p>
                </div>
                <div class="p-6">
                    <canvas id="chartPelanggaran" height="200"></canvas>
                </div>
            </div>

            <!-- Chart 2: Kelas Ternakal -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/30">
                    <h3 class="text-sm font-bold text-slate-700 m-0">🏫 Kelas Ternakal</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Top 10 kelas</p>
                </div>
                <div class="p-6">
                    <canvas id="chartKelas" height="200"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
// Chart 1: Pelanggaran Populer
new Chart(document.getElementById('chartPelanggaran'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Jumlah Pelanggaran',
            data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 10,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0, font: { size: 10 } },
                grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: {
                ticks: { font: { size: 9 }, maxRotation: 45, minRotation: 45 },
                grid: { display: false }
            }
        }
    }
});

// Chart 2: Kelas Ternakal
new Chart(document.getElementById('chartKelas'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartKelasLabels) !!},
        datasets: [{
            label: 'Jumlah Pelanggaran',
            data: {!! json_encode($chartKelasData) !!},
            backgroundColor: 'rgba(239, 68, 68, 0.8)',
            borderColor: 'rgba(239, 68, 68, 1)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 10,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0, font: { size: 10 } },
                grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: {
                ticks: { font: { size: 9 }, maxRotation: 45, minRotation: 45 },
                grid: { display: false }
            }
        }
    }
});
</script>

<style>
    .page-wrap { background: #f8fafc; min-height: 100vh; padding: 1.5rem; font-family: 'Inter', sans-serif; }
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }
    .stat-card { background: white; border-radius: 1rem; padding: 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; height: 100%; }
    .float-table { border-collapse: separate; border-spacing: 0 4px; width: 100%; }
</style>

@endsection