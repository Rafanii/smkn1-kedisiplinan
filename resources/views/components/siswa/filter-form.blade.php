<!-- Filter Form Partial - Siswa Index Page -->
<!-- Location: resources/views/components/siswa/filter-form.blade.php -->
<!-- Purpose: Clean separation of filter UI from main blade -->
<!-- JS Handler: public/js/pages/siswa/filters.js -->
<!-- CSS Styling: public/css/pages/siswa/filters.css -->

{{-- Siswa filter form only - no outer card. Index page must wrap this inside a card with id="stickyFilter" --}}
<form id="filterForm" action="{{ route('siswa.index') }}" method="GET">
    <div class="row align-items-end">

        {{-- If user is Wali Kelas, we hide tingkat/jurusan/kelas filters (they only see their class) --}}
        @if(!$isWaliKelas)
            {{-- For Kaprodi: show only Tingkat and Kelas (Kelas already limited in controller to jurusan) --}}
            <div class="col-md-3 mb-2">
                <label class="filter-label">Tingkat</label>
                <select name="tingkat" class="form-control form-control-sm form-control-clean filter-select" data-filter="tingkat" onchange="document.getElementById('filterForm').submit()">
                    <option value="">- Semua -</option>
                    <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
            </div>

            @if(!($isKaprodi ?? false))
                {{-- Only show Jurusan filter for non-Kaprodi (operator/waka) --}}
                <div class="col-md-3 mb-2">
                    <label class="filter-label">Jurusan</label>
                    <select name="jurusan_id" class="form-control form-control-sm form-control-clean filter-select" data-filter="jurusan" onchange="document.getElementById('filterForm').submit()">
                        <option value="">- Semua -</option>
                        @foreach($allJurusan as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-3 mb-2">
                <label class="filter-label">Kelas</label>
                <select name="kelas_id" class="form-control form-control-sm form-control-clean filter-select" data-filter="kelas" onchange="document.getElementById('filterForm').submit()">
                    <option value="">- Semua -</option>
                    @foreach($allKelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="col-md-3 mb-2">
            <label class="filter-label">Cari Siswa</label>
            <div class="input-group input-group-sm">
                <input type="text" id="liveSearch" name="cari" class="form-control form-control-clean filter-search" placeholder="Ketik Nama atau NISN..." value="{{ request('cari') }}" data-filter="search" oninput="clearTimeout(window._siswaSearchDebounce); window._siswaSearchDebounce=setTimeout(function(){document.getElementById('filterForm').submit();}, 800)">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>

        @if($isWaliKelas)
            <div class="col-md-2 mb-2 text-right">
                <a href="{{ route('siswa.index') }}" class="btn btn-default btn-sm btn-block filter-reset-btn" title="Hapus Pencarian">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        @else
            @if(request()->has('cari') || request()->has('kelas_id') || request()->has('tingkat') || request()->has('jurusan_id'))
                <div class="col-12 mt-2 text-right border-top pt-2">
                    <a href="{{ route('siswa.index') }}" class="btn btn-xs text-danger font-weight-bold filter-reset-btn">
                        <i class="fas fa-times-circle"></i> Reset Filter
                    </a>
                </div>
            @endif
        @endif

    </div>
</form>
