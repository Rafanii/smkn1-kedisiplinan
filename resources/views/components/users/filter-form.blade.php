{{-- Filter Form Partial for Users Management --}}
{{-- Usage: @include('components.users.filter-form') --}}

<form id="filterForm" action="{{ route('users.index') }}" method="GET">
    <div class="row align-items-end">

        <div class="col-md-4 mb-2">
            <label class="filter-label">Role (Jabatan)</label>
            <select name="role_id" class="form-control form-control-sm form-control-clean" data-filter="role_id">
                <option value="">- Semua Role -</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->nama_role }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-2">
            <label class="filter-label">Cari Nama / Username / Email</label>
            <div class="input-group input-group-sm">
                <input type="text" name="cari" class="form-control form-control-clean" placeholder="Ketik kata kunci..." value="{{ request('cari') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>

        <div class="col-md-2 mb-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100" id="filterApplyBtn">
                <i class="fas fa-filter mr-1"></i> Terapkan
            </button>
        </div>

        @if(request()->has('cari') || request()->has('role_id'))
            <div class="col-12 mt-2 pt-2 border-top text-right">
                <a href="{{ route('users.index') }}" class="btn btn-xs text-danger filter-reset-btn">
                    <i class="fas fa-undo mr-1"></i> Reset Filter
                </a>
            </div>
        @endif

    </div>
</form>
