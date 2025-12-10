@extends('layouts.app')

@section('title', 'Kelola Frequency Rules')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-sliders-h mr-2"></i> Kelola Aturan & Frequency Rules</h4>
            <p class="text-muted">
                Kelola semua jenis pelanggaran, poin, sanksi, dan frequency rules.
                <br><strong>Status Aktif/Nonaktif:</strong> Toggle untuk mengaktifkan/menonaktifkan pelanggaran. Pelanggaran nonaktif tidak akan muncul di form pencatatan.
                <br><strong>Frequency Rules:</strong> Atur poin berdasarkan frekuensi (berapa kali siswa melakukan pelanggaran).
                <br><strong>Poin Default:</strong> Untuk pelanggaran tanpa frequency rules (langsung dapat poin setiap kali tercatat).
            </p>
        </div>
    </div>

    <!-- Filter & Actions -->
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="GET" action="{{ route('frequency-rules.index') }}">
                <div class="input-group">
                    <select name="kategori_id" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('frequency-rules.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-8 text-right">
            <a href="{{ route('jenis-pelanggaran.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Tambah Jenis Pelanggaran Baru
            </a>
        </div>
    </div>

    <!-- Table Jenis Pelanggaran -->
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="8%">Kategori</th>
                        <th width="22%">Nama Pelanggaran</th>
                        <th width="35%">Rules: Frekuensi, Poin & Sanksi</th>
                        <th width="12%" class="text-center">Status</th>
                        <th width="23%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisPelanggaran as $jp)
                    <tr>
                        <td>
                            @php
                                $kategoriNama = $jp->kategoriPelanggaran->nama_kategori ?? 'Unknown';
                                $badgeClass = $kategoriNama == 'Ringan' ? 'info' : ($kategoriNama == 'Sedang' ? 'warning' : 'danger');
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">{{ $kategoriNama }}</span>
                        </td>
                        <td>
                            <strong>{{ $jp->nama_pelanggaran }}</strong>
                            <br><small class="text-muted">ID: {{ $jp->id }}</small>
                        </td>
                        <td>
                            @if($jp->frequencyRules->count() > 0)
                                @foreach($jp->frequencyRules as $rule)
                                    <div class="mb-2 p-2 border-left border-primary" style="border-left-width: 3px !important;">
                                        <div>
                                            <span class="badge badge-primary">
                                                @if($rule->frequency_min == 1 && !$rule->frequency_max)
                                                    Setiap kejadian
                                                @elseif($rule->frequency_max)
                                                    {{ $rule->frequency_min }}-{{ $rule->frequency_max }}x
                                                @else
                                                    {{ $rule->frequency_min }}+x
                                                @endif
                                            </span>
                                            <span class="badge badge-danger">{{ $rule->poin }} poin</span>
                                            @if($rule->trigger_surat)
                                                <span class="badge badge-warning"><i class="fas fa-envelope"></i> Surat</span>
                                            @endif
                                        </div>
                                        <small class="text-dark"><strong>Sanksi:</strong> {{ $rule->sanksi_description }}</small>
                                        <br><small class="text-muted"><strong>Pembina:</strong> 
                                            @foreach($rule->pembina_roles as $role)
                                                @if($role == 'Semua Guru & Staff')
                                                    <span class="badge badge-primary"><i class="fas fa-users"></i> {{ $role }}</span>
                                                @else
                                                    {{ $role }}{{ !$loop->last ? ', ' : '' }}
                                                @endif
                                            @endforeach
                                        </small>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-2">
                                    <span class="badge badge-secondary">{{ $jp->poin }} poin</span>
                                    <span class="badge badge-info">Setiap kejadian</span>
                                    <br><small class="text-muted"><em>Belum ada frequency rules. Klik "Kelola Rules" untuk menambahkan.</em></small>
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($jp->frequencyRules->count() > 0)
                                <div class="custom-control custom-switch custom-switch-lg">
                                    <input type="checkbox" 
                                           class="custom-control-input toggle-active" 
                                           id="toggle-active-{{ $jp->id }}"
                                           data-id="{{ $jp->id }}"
                                           {{ $jp->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="toggle-active-{{ $jp->id }}">
                                        @if($jp->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @endif
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">{{ $jp->frequencyRules->count() }} rules</small>
                            @else
                                <span class="badge badge-secondary badge-lg">Nonaktif</span>
                                <br><small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-triangle"></i> Belum ada rules
                                </small>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('frequency-rules.show', $jp->id) }}" class="btn btn-sm btn-primary" title="Kelola Frequency Rules">
                                <i class="fas fa-sliders-h"></i> Kelola Rules
                            </a>
                            <a href="{{ route('jenis-pelanggaran.edit', $jp->id) }}" class="btn btn-sm btn-warning" title="Edit Jenis Pelanggaran">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



@push('scripts')
<script>
$(document).ready(function() {
    $('.toggle-active').change(function() {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const isChecked = checkbox.is(':checked');
        const label = checkbox.siblings('label');
        
        $.ajax({
            url: `/frequency-rules/${id}/toggle-active`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update badge langsung tanpa reload
                    if (response.is_active) {
                        label.html('<span class="badge badge-success">Aktif</span>');
                    } else {
                        label.html('<span class="badge badge-secondary">Nonaktif</span>');
                    }
                    toastr.success(response.message);
                }
            },
            error: function() {
                // Revert checkbox jika error
                checkbox.prop('checked', !isChecked);
                toastr.error('Gagal mengubah status pelanggaran');
            }
        });
    });
});
</script>
@endpush
@endsection
