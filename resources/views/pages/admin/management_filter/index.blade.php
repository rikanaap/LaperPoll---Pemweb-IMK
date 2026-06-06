@extends('layouts.admin')

@section('title', 'Manajemen Filter')
@section('page-title', 'Manajemen Filter')
@section('breadcrumb', 'Admin / Filter')

@section('content')

<x-admin.alert />

<div class="page-header">
    <div class="page-header__left">
        <h1>Daftar Filter</h1>
        <p>Kelola semua kategori dan filter resep di platform</p>
    </div>
    <button class="btn btn--primary" onclick="openCreateModal()">
        <span class="material-icons-round">add</span>
        Tambah Filter
    </button>
</div>

<div class="card">

    {{-- Filter Bar --}}
    <div class="card__header">
        <form method="GET" action="{{ route('admin.filter.index') }}" class="filter-bar" id="filterForm">

            <div class="filter-bar__search">
                <span class="material-icons-round">search</span>
                <input
                    type="text"
                    name="search"
                    placeholder="Cari nama atau deskripsi filter..."
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
            </div>

            @php
                $level = request('level') ?? '';
                $levelLabels = [
                    '' => 'Semua Level',
                ];
                foreach($availableLevels as $lvl) {
                    $levelLabels[$lvl] = \App\Models\Filter::levelLabel($lvl);
                }
            @endphp
            <div class="custom-select">
                <input type="hidden" name="level" value="{{ $level }}">
                <div class="custom-select__trigger">
                    <span class="custom-select__label">
                        {{ $levelLabels[$level] ?? 'Semua Level' }}
                    </span>
                    <span class="material-icons-round custom-select__arrow">expand_more</span>
                </div>
                <div class="custom-select__dropdown">
                    <div class="custom-select__option {{ $level === '' ? 'is-selected' : '' }}" data-value="">Semua Level</div>
                    @foreach($availableLevels as $lvl)
                        <div class="custom-select__option {{ $level == $lvl ? 'is-selected' : '' }}" data-value="{{ $lvl }}">
                            {{ \App\Models\Filter::levelLabel($lvl) }}
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" hidden>Cari</button>

            <!-- @if(request('level'))
                <a href="{{ route('admin.filter.index') }}" class="btn btn--secondary btn--sm">
                    <span class="material-icons-round">close</span>
                    Reset
                </a>
            @endif -->

        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Nama Filter</th>
                    <th>Tipe</th>
                    <th>Deskripsi</th>
                    <th>Dipakai di Resep</th>
                    <th>Ditambahkan</th>
                    <th style="width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filterList as $item)
                <tr>
                    <td class="td-sub">{{ $filterList->firstItem() + $loop->index }}</td>

                    <td>
                        <div class="td-name">{{ $item->title }}</div>
                    </td>

                    {{-- ✅ Badge pakai levelColor() dan levelLabel() --}}
                    <td>
                        @if(! is_null($item->level))
                            <span class="badge {{ \App\Models\Filter::levelColor($item->level) }}">
                                {{ \App\Models\Filter::levelLabel($item->level) }}
                            </span>
                        @else
                            <span class="td-sub">—</span>
                        @endif
                    </td>

                    <td class="td-desc">
                        {{ $item->description ? Str::limit($item->description, 60) : '—' }}
                    </td>

                    <td>
                        @if($item->reseps_count > 0)
                            <span class="badge badge--blue">
                                <span class="material-icons-round">menu_book</span>
                                {{ number_format($item->reseps_count) }} resep
                            </span>
                        @else
                            <span class="td-sub">Belum dipakai</span>
                        @endif
                    </td>

                    <td class="td-sub">{{ $item->created_at->format('d M Y') }}</td>

                    <td>
                        <div class="td-actions">

                            <button
                                class="icon-btn"
                                title="Edit Filter"
                                onclick="openEditModal(
                                    {{ $item->id }},
                                    '{{ addslashes($item->title) }}',
                                    {{ $item->level ?? 'null' }},
                                    '{{ addslashes($item->description ?? '') }}',
                                    '{{ route('admin.filter.update', $item) }}'
                                )"
                            >
                                <span class="material-icons-round">edit</span>
                            </button>

                            <button
                                class="icon-btn icon-btn--danger"
                                title="Hapus Filter"
                                onclick="confirmDelete(
                                    '{{ route('admin.filter.destroy', $item) }}',
                                    '{{ addslashes($item->title) }}',
                                    {{ $item->reseps_count }}
                                )"
                            >
                                <span class="material-icons-round">delete</span>
                            </button>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <span class="material-icons-round">filter_list</span>
                            <h3>Belum ada filter</h3>
                            <p>
                               {{ request('level')
                                ? 'Tidak ada filter yang cocok dengan pencarian.'
                                : 'Tambahkan filter pertama untuk digunakan pada resep.' }}
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($filterList->hasPages())
    <div class="pagination">
        <span class="pagination__info">
            Menampilkan {{ $filterList->firstItem() }}–{{ $filterList->lastItem() }}
            dari {{ number_format($filterList->total()) }} filter
        </span>
        <div class="pagination__btns">

            <button
                class="pagination__btn"
                {{ $filterList->onFirstPage() ? 'disabled' : '' }}
                onclick="window.location='{{ $filterList->previousPageUrl() }}'"
            >
                <span class="material-icons-round">chevron_left</span>
            </button>

            @php
                $cp = $filterList->currentPage();
                $lp = $filterList->lastPage();
                $s  = max(1, $cp - 3);
                $e  = min($lp, $cp + 3);
            @endphp

            @if($s > 1)
                <button class="pagination__btn" onclick="window.location='{{ $filterList->url(1) }}'">1</button>
                @if($s > 2)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
            @endif

            @for($i = $s; $i <= $e; $i++)
                <button
                    class="pagination__btn {{ $cp === $i ? 'is-active' : '' }}"
                    onclick="window.location='{{ $filterList->url($i) }}'"
                >{{ $i }}</button>
            @endfor

            @if($e < $lp)
                @if($e < $lp - 1)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
                <button class="pagination__btn" onclick="window.location='{{ $filterList->url($lp) }}'">{{ $lp }}</button>
            @endif

            <button
                class="pagination__btn"
                {{ ! $filterList->hasMorePages() ? 'disabled' : '' }}
                onclick="window.location='{{ $filterList->nextPageUrl() }}'"
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
const CSRF_TOKEN  = '{{ csrf_token() }}';
const ROUTE_STORE = '{{ route("admin.filter.store") }}';

function setInputValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value ?? '';
}

function submitForm(action, method, fields) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    const addHidden = (name, value) => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = name;
        input.value = value ?? '';
        form.appendChild(input);
    };

    addHidden('_token', CSRF_TOKEN);
    if (method !== 'POST') addHidden('_method', method);
    Object.entries(fields).forEach(([k, v]) => addHidden(k, v));

    document.body.appendChild(form);
    form.submit();
}

function buildModalBody() {
    return `
        <div class="form-group">
            <label class="form-label">Nama Filter <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="contoh: Vegetarian" id="inputTitle">
        </div>
        <div class="form-group">
            <label class="form-label">
                Tipe
                <span class="text-muted">(opsional)</span>
            </label>
            <select class="form-control" id="inputLevel">
                <option value="">— Pilih Tipe —</option>
                <option value="1">Jenis Makanan</option>
                <option value="2">Metode Masak</option>
                <option value="3">Rasa / Preferensi</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">
                Deskripsi
                <span class="text-muted">(opsional)</span>
            </label>
            <textarea class="form-control" placeholder="Deskripsi singkat tentang filter ini..."
                id="inputDescription" rows="3"></textarea>
        </div>
    `;
}

// ── Create Modal ──────────────────────────────────────────
function openCreateModal() {
    openModal(
        'Tambah Filter Baru',
        buildModalBody(),
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
         <button class="btn btn--primary" onclick="submitCreate()">
             <span class="material-icons-round">add</span> Simpan
         </button>`
    );
    setTimeout(() => document.getElementById('inputTitle')?.focus(), 100);
}

function submitCreate() {
    const title       = document.getElementById('inputTitle').value.trim();
    const level       = document.getElementById('inputLevel').value;
    const description = document.getElementById('inputDescription').value.trim();

    if (!title) { alert('Nama filter wajib diisi.'); return; }

    submitForm(ROUTE_STORE, 'POST', { title, level, description });
}

// ── Edit Modal ────────────────────────────────────────────
function openEditModal(id, title, level, description, route) {
    openModal(
        'Edit Filter',
        buildModalBody(),
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
         <button class="btn btn--primary" onclick="submitEdit('${route}')">
             <span class="material-icons-round">save</span> Simpan
         </button>`
    );

    // ✅ Set value via property setelah modal render
    setInputValue('inputTitle', title);
    setInputValue('inputDescription', description);
    document.getElementById('inputLevel').value = level ?? '';
    setTimeout(() => document.getElementById('inputTitle')?.focus(), 100);
}

function submitEdit(route) {
    const title       = document.getElementById('inputTitle').value.trim();
    const level       = document.getElementById('inputLevel').value;
    const description = document.getElementById('inputDescription').value.trim();

    if (!title) { alert('Nama filter wajib diisi.'); return; }

    submitForm(route, 'PATCH', { title, level, description });
}

// ── Delete ────────────────────────────────────────────────
function confirmDelete(url, title, resepCount) {
    if (resepCount > 0) {
        openModal(
            'Tidak Bisa Dihapus',
            `<p style="font-size:.875rem;color:var(--text-secondary)">
                Filter <strong>${title}</strong> tidak bisa dihapus karena masih digunakan
                di <strong>${resepCount} resep</strong>. Hapus atau edit resep terkait terlebih dahulu.
            </p>`,
            `<button class="btn btn--secondary" onclick="closeModal()">Mengerti</button>`
        );
        return;
    }
    openModal(
        'Hapus Filter',
        `<p style="font-size:.875rem;color:var(--text-secondary)">
            Yakin ingin menghapus filter <strong>${title}</strong>?
            Tindakan ini <strong>tidak dapat dibatalkan</strong>.
        </p>`,
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
         <button class="btn btn--danger" onclick="doDelete('${url}')">
             <span class="material-icons-round">delete</span> Hapus
         </button>`
    );
}

function doDelete(url) {
    closeModal();
    submitForm(url, 'DELETE', {});
}

// ── Search debounce ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('input[name="search"]');
    if (!input) return;
    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => filterForm.submit(), 400);
    });
});
</script>
@endpush