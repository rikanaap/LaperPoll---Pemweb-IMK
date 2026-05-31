@extends('layouts.admin')

@section('title', 'Manajemen Bahan')
@section('page-title', 'Manajemen Bahan')
@section('breadcrumb', 'Admin / Bahan')

@section('content')

{{-- ── Flash Message ─────────────────────────────────────────── --}}
@if(session('success'))
    <div class="alert alert--success">
        <span class="material-icons-round">check_circle</span>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert--error">
        <span class="material-icons-round">error</span>
        {{ session('error') }}
    </div>
@endif

{{-- ── Page Header ───────────────────────────────────────────── --}}
<div class="page-header">
    <div class="page-header__left">
        <h1>Daftar Bahan</h1>
        <p>Kelola semua bahan yang tersedia di platform</p>
    </div>
    <button class="btn btn--primary" onclick="openCreateModal()">
        <span class="material-icons-round">add</span>
        Tambah Bahan
    </button>
</div>

{{-- ── Table Card ────────────────────────────────────────────── --}}
<div class="card">

    {{-- Filter Bar --}}
    <div class="card__header">
        <form method="GET" action="{{ route('admin.bahan.index') }}" class="filter-bar" id="filterForm">

            <div class="filter-bar__search">
                <span class="material-icons-round">search</span>
                <input
                    type="text"
                    name="search"
                    placeholder="Cari nama bahan..."
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
            </div>

            <select name="expired" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Bahan</option>
                <option value="yes" {{ request('expired') === 'yes' ? 'selected' : '' }}>Ada Expired</option>
                <option value="no"  {{ request('expired') === 'no'  ? 'selected' : '' }}>Tanpa Expired</option>
            </select>

            <button type="submit" style="display:none">Cari</button>

            @if(request()->hasAny(['search', 'expired']))
                <a href="{{ route('admin.bahan.index') }}" class="btn btn--secondary btn--sm">
                    <span class="material-icons-round">close</span>
                    Reset
                </a>
            @endif

        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Nama Bahan</th>
                    <th>Ekspektasi Expired</th>
                    <th>Dipakai di Resep</th>
                    <th>Ditambahkan</th>
                    <th style="width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bahans as $bahan)
                <tr>
                    <td class="td-sub">{{ $bahans->firstItem() + $loop->index }}</td>

                    {{-- Nama --}}
                    <td>
                        <div class="td-name">{{ $bahan->nama }}</div>
                    </td>

                    {{-- Expired --}}
                    <td>
                        @if($bahan->expired_expectancy_day)
                            <span class="badge badge--orange">
                                <span class="material-icons-round">schedule</span>
                                {{ number_format($bahan->expired_expectancy_day) }} hari
                            </span>
                        @else
                            <span class="td-sub">—</span>
                        @endif
                    </td>

                    {{-- Jumlah Resep --}}
                    <td>
                        @if($bahan->reseps_count > 0)
                            <span class="badge badge--blue">
                                <span class="material-icons-round">menu_book</span>
                                {{ number_format($bahan->reseps_count) }} resep
                            </span>
                        @else
                            <span class="td-sub">Belum dipakai</span>
                        @endif
                    </td>

                    {{-- Tanggal --}}
                    <td class="td-sub">{{ $bahan->created_at->format('d M Y') }}</td>

                    {{-- Aksi --}}
                    <td>
                        <div class="td-actions">

                            {{-- Edit --}}
                            <button
                                class="icon-btn"
                                title="Edit Bahan"
                                onclick="openEditModal(
                                    {{ $bahan->id }},
                                    '{{ addslashes($bahan->nama) }}',
                                    {{ $bahan->expired_expectancy_day ?? 'null' }}
                                )"
                            >
                                <span class="material-icons-round">edit</span>
                            </button>

                            {{-- Hapus --}}
                            <button
                                class="icon-btn icon-btn--danger"
                                title="Hapus Bahan"
                                onclick="confirmDelete(
                                    '{{ route('admin.bahan.destroy', $bahan) }}',
                                    '{{ addslashes($bahan->nama) }}',
                                    {{ $bahan->reseps_count }}
                                )"
                            >
                                <span class="material-icons-round">delete</span>
                            </button>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <span class="material-icons-round">kitchen</span>
                            <h3>Belum ada bahan</h3>
                            <p>
                                @if(request()->hasAny(['search', 'expired']))
                                    Tidak ada bahan yang cocok dengan filter yang dipilih.
                                @else
                                    Tambahkan bahan pertama untuk mulai digunakan di resep.
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($bahans->hasPages())
    <div class="pagination">
        <span class="pagination__info">
            Menampilkan {{ $bahans->firstItem() }}–{{ $bahans->lastItem() }}
            dari {{ number_format($bahans->total()) }} bahan
        </span>
        <div class="pagination__btns">

            <button
                class="pagination__btn"
                {{ $bahans->onFirstPage() ? 'disabled' : '' }}
                onclick="window.location='{{ $bahans->previousPageUrl() }}'"
            >
                <span class="material-icons-round">chevron_left</span>
            </button>

            @php
                $currentPage = $bahans->currentPage();
                $lastPage    = $bahans->lastPage();
                $start       = max(1, $currentPage - 3);
                $end         = min($lastPage, $currentPage + 3);
            @endphp

            @if($start > 1)
                <button class="pagination__btn" onclick="window.location='{{ $bahans->url(1) }}'">1</button>
                @if($start > 2)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
            @endif

            @for($i = $start; $i <= $end; $i++)
                <button
                    class="pagination__btn {{ $currentPage === $i ? 'is-active' : '' }}"
                    onclick="window.location='{{ $bahans->url($i) }}'"
                >{{ $i }}</button>
            @endfor

            @if($end < $lastPage)
                @if($end < $lastPage - 1)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
                <button class="pagination__btn" onclick="window.location='{{ $bahans->url($lastPage) }}'">
                    {{ $lastPage }}
                </button>
            @endif

            <button
                class="pagination__btn"
                {{ ! $bahans->hasMorePages() ? 'disabled' : '' }}
                onclick="window.location='{{ $bahans->nextPageUrl() }}'"
            >
                <span class="material-icons-round">chevron_right</span>
            </button>

        </div>
    </div>
    @endif

</div>

@endsection


@push('scripts')
<script>

function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function openCreateModal() {
    openModal(
        'Tambah Bahan Baru',
        `<div class="form-group">
            <label class="form-label">Nama Bahan <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" placeholder="contoh: Bawang Merah" id="newNama">
        </div>
        <div class="form-group">
            <label class="form-label">
                Ekspektasi Expired
                <span style="color:var(--text-muted);font-weight:500">(opsional, dalam hari)</span>
            </label>
            <input
                type="number"
                class="form-control"
                placeholder="contoh: 7"
                id="newExpired"
                min="1"
                max="3650"
            >
            <span class="form-hint">Kosongkan jika bahan tidak memiliki estimasi kadaluarsa.</span>
        </div>`,
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
        <button class="btn btn--primary" onclick="submitCreate()">
            <span class="material-icons-round">add</span> Simpan
        </button>`
    );


    setTimeout(() => document.getElementById('newNama')?.focus(), 100);
}

function openEditModal(id, nama, expired) {
    openModal(
        'Edit Bahan',
        `<div class="form-group">
            <label class="form-label">Nama Bahan <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" value="${escHtml(nama)}" id="editNama">
        </div>
        <div class="form-group">
            <label class="form-label">
                Ekspektasi Expired
                <span style="color:var(--text-muted);font-weight:500">(opsional, dalam hari)</span>
            </label>
            <input
                type="number"
                class="form-control"
                value="${expired ?? ''}"
                placeholder="contoh: 7"
                id="editExpired"
                min="1"
                max="3650"
            >
            <span class="form-hint">Kosongkan jika bahan tidak memiliki estimasi kadaluarsa.</span>
        </div>`,
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
        <button class="btn btn--primary" onclick="submitEdit(${id})">
            <span class="material-icons-round">save</span> Simpan
        </button>`
    );

    setTimeout(() => document.getElementById('editNama')?.focus(), 100);
}

function submitCreate() {
    const nama    = document.getElementById('newNama').value.trim();
    const expired = document.getElementById('newExpired').value.trim();

    if (!nama) {
        alert('Nama bahan wajib diisi.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.bahan.store") }}';
    form.innerHTML = `
        <input type="hidden" name="_token"                   value="{{ csrf_token() }}">
        <input type="hidden" name="nama"                     value="${escHtml(nama)}">
        <input type="hidden" name="expired_expectancy_day"   value="${escHtml(expired)}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function submitEdit(id) {
    const nama    = document.getElementById('editNama').value.trim();
    const expired = document.getElementById('editExpired').value.trim();

    if (!nama) {
        alert('Nama bahan wajib diisi.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/bahan/${id}`;
    form.innerHTML = `
        <input type="hidden" name="_token"                  value="{{ csrf_token() }}">
        <input type="hidden" name="_method"                 value="PATCH">
        <input type="hidden" name="nama"                    value="${escHtml(nama)}">
        <input type="hidden" name="expired_expectancy_day"  value="${escHtml(expired)}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function confirmDelete(url, nama, resepCount) {
    let msg = `Hapus bahan "${nama}"?`;

    if (resepCount > 0) {
        msg += `\n\n⚠️ Bahan ini masih digunakan di ${resepCount} resep dan tidak bisa dihapus.`;
        alert(msg);
        return;
    }

    msg += '\nTindakan ini tidak dapat dibatalkan.';
    if (!confirm(msg)) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.innerHTML = `
        <input type="hidden" name="_token"  value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('input[name="search"]');
    if (!searchInput) return;

    let timer;
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => document.getElementById('filterForm').submit(), 400);
    });
});
</script>
@endpush