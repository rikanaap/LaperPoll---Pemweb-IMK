@extends('layouts.admin')

@section('title', 'Manajemen Bahan')
@section('page-title', 'Manajemen Bahan')
@section('breadcrumb', 'Admin / Bahan')

@section('content')

<x-admin.alert />

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

            <select name="expired" onchange="filterForm.submit()">
                <option value="">Semua Bahan</option>
                <option value="yes" {{ request('expired') === 'yes' ? 'selected' : '' }}>Ada Expired</option>
                <option value="no"  {{ request('expired') === 'no'  ? 'selected' : '' }}>Tanpa Expired</option>
            </select>

            <button type="submit" hidden>Cari</button>

            @if(request('expired'))
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
                    <th>Kategori</th>
                    <th>Dipakai di Resep</th>
                    <th>Ditambahkan</th>
                    <th style="width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bahans as $bahan)
                <tr>
                    <td class="td-sub">{{ $bahans->firstItem() + $loop->index }}</td>

                    <td>
                        <div class="td-name">{{ $bahan->nama }}</div>
                    </td>

                    <td>
                        @if($bahan->kategori)
                            <span class="badge badge--green">{{ $bahan->kategori }}</span>
                        @else
                            <span class="td-sub">—</span>
                        @endif
                    </td>

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

                    <td class="td-sub">{{ $bahan->created_at->format('d M Y') }}</td>

                    <td>
                        <div class="td-actions">

                            <button
                                class="icon-btn"
                                title="Edit Bahan"
                                onclick="openEditModal(
                                    {{ $bahan->id }},
                                    '{{ addslashes($bahan->nama) }}',
                                    {{ $bahan->expired_expectancy_day ?? 'null' }},
                                    '{{ route('admin.bahan.update', $bahan) }}',
                                    '{{ $bahan->kategori ?? '' }}'
                                )"
                            >
                                <span class="material-icons-round">edit</span>
                            </button>

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
                    <td colspan="7">
                        <div class="empty-state">
                            <span class="material-icons-round">kitchen</span>
                            <h3>Belum ada bahan</h3>
                            <p>
                                {{ request()->hasAny(['search', 'expired'])
                                    ? 'Tidak ada bahan yang cocok dengan filter yang dipilih.'
                                    : 'Tambahkan bahan pertama untuk mulai digunakan di resep.' }}
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
                $cp = $bahans->currentPage();
                $lp = $bahans->lastPage();
                $s  = max(1, $cp - 3);
                $e  = min($lp, $cp + 3);
            @endphp

            @if($s > 1)
                <button class="pagination__btn" onclick="window.location='{{ $bahans->url(1) }}'">1</button>
                @if($s > 2)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
            @endif

            @for($i = $s; $i <= $e; $i++)
                <button
                    class="pagination__btn {{ $cp === $i ? 'is-active' : '' }}"
                    onclick="window.location='{{ $bahans->url($i) }}'"
                >{{ $i }}</button>
            @endfor

            @if($e < $lp)
                @if($e < $lp - 1)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
                <button class="pagination__btn" onclick="window.location='{{ $bahans->url($lp) }}'">{{ $lp }}</button>
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
const CSRF_TOKEN  = '{{ csrf_token() }}';
const ROUTE_STORE = '{{ route("admin.bahan.store") }}';

// ── Error state dari server (untuk auto-buka modal) ──
const SERVER_ERRORS = @json($errors->toArray());
const OLD_INPUT     = @json(old());

function setInputValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value ?? '';
}

function showInlineError(containerId, errors) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';
    Object.values(errors).flat().forEach(msg => {
        const div = document.createElement('div');
        div.className   = 'form-hint';
        div.style.color = 'var(--red, #ef4444)';
        div.textContent = msg;
        container.appendChild(div);
    });
}

function submitForm(action, method, fields) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    const addHidden = (name, value) => {
        const input   = document.createElement('input');
        input.type    = 'hidden';
        input.name    = name;
        input.value   = value ?? '';
        form.appendChild(input);
    };

    addHidden('_token', CSRF_TOKEN);
    if (method !== 'POST') addHidden('_method', method);
    Object.entries(fields).forEach(([k, v]) => addHidden(k, v));

    document.body.appendChild(form);
    form.submit();
}

// ── KATEGORI OPTIONS HTML ─────────────────────────────────────────────────
function kategoriOptions(selected = '') {
    const list = ['', 'KARBOHIDRAT', 'PROTEIN', 'SAYURAN', 'BUAH', 'BUMBU', 'MINUMAN', 'LAINNYA'];
    return list.map(k => {
        const label = k || '— Pilih Kategori —';
        const sel   = k === selected ? ' selected' : '';
        return `<option value="${k}"${sel}>${label}</option>`;
    }).join('');
}

// ── Create Modal ──────────────────────────────────────────────────────────
function openCreateModal() {
    openModal(
        'Tambah Bahan Baru',
        `<div class="form-group">
            <label class="form-label">Nama Bahan <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="contoh: Bawang Merah" id="newNama">
        </div>
        <div class="form-group">
            <label class="form-label">Kategori <span class="text-muted">(opsional)</span></label>
            <select class="form-control" id="newKategori">${kategoriOptions()}</select>
            <span class="form-hint">Dipakai untuk pengelompokan di Nota Belanja</span>
        </div>
        <div class="form-group">
            <label class="form-label">Ekspektasi Expired <span class="text-muted">(opsional, dalam hari)</span></label>
            <input type="number" class="form-control" placeholder="contoh: 7" id="newExpired" min="1" max="3650">
            <span class="form-hint">Kosongkan jika tidak memiliki estimasi kadaluarsa.</span>
        </div>
        <div id="createErrors"></div>`,
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
         <button class="btn btn--primary" onclick="submitCreate()">
             <span class="material-icons-round">add</span> Simpan
         </button>`
    );
    setTimeout(() => document.getElementById('newNama')?.focus(), 100);
}

function submitCreate() {
    const nama     = document.getElementById('newNama').value.trim();
    const kategori = document.getElementById('newKategori').value;
    const expired  = document.getElementById('newExpired').value.trim();

    // Validasi frontend inline — tidak pakai alert()
    if (!nama) {
        document.getElementById('createErrors').innerHTML =
            '<div class="form-hint" style="color:var(--red,#ef4444)">Nama bahan wajib diisi.</div>';
        document.getElementById('newNama')?.focus();
        return;
    }
    document.getElementById('createErrors').innerHTML = '';

    submitForm(ROUTE_STORE, 'POST', {
        nama,
        kategori,
        expired_expectancy_day: expired,
    });
}

// ── Edit Modal ────────────────────────────────────────────────────────────
function openEditModal(id, nama, expired, route, kategori = '') {
    openModal(
        'Edit Bahan',
        `<div class="form-group">
            <label class="form-label">Nama Bahan <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editNama">
        </div>
        <div class="form-group">
            <label class="form-label">Kategori <span class="text-muted">(opsional)</span></label>
            <select class="form-control" id="editKategori">${kategoriOptions(kategori)}</select>
            <span class="form-hint">Dipakai untuk pengelompokan di Nota Belanja</span>
        </div>
        <div class="form-group">
            <label class="form-label">Ekspektasi Expired <span class="text-muted">(opsional, dalam hari)</span></label>
            <input type="number" class="form-control" placeholder="contoh: 7" id="editExpired" min="1" max="3650">
            <span class="form-hint">Kosongkan jika tidak memiliki estimasi kadaluarsa.</span>
        </div>
        <div id="editErrors"></div>`,
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
         <button class="btn btn--primary" onclick="submitEdit('${route}')">
             <span class="material-icons-round">save</span> Simpan
         </button>`
    );

    setInputValue('editNama',    nama);
    setInputValue('editExpired', expired);
    // kategori sudah di-set via kategoriOptions(kategori) di HTML
    setTimeout(() => document.getElementById('editNama')?.focus(), 100);
}

function submitEdit(route) {
    const nama     = document.getElementById('editNama').value.trim();
    const kategori = document.getElementById('editKategori').value;
    const expired  = document.getElementById('editExpired').value.trim();

    // Validasi frontend inline — tidak pakai alert()
    if (!nama) {
        document.getElementById('editErrors').innerHTML =
            '<div class="form-hint" style="color:var(--red,#ef4444)">Nama bahan wajib diisi.</div>';
        document.getElementById('editNama')?.focus();
        return;
    }
    document.getElementById('editErrors').innerHTML = '';

    submitForm(route, 'PATCH', {
        nama,
        kategori,
        expired_expectancy_day: expired,
    });
}

// ── Delete Confirm — pakai openModal bukan alert() ───────────────────────
function confirmDelete(url, nama, resepCount) {
    if (resepCount > 0) {
        openModal(
            'Tidak Bisa Dihapus',
            `<p style="font-size:.875rem;color:var(--text-secondary)">
                Bahan <strong>${nama}</strong> tidak bisa dihapus karena masih digunakan
                di <strong>${resepCount} resep</strong>. Hapus atau edit resep terkait terlebih dahulu.
            </p>`,
            `<button class="btn btn--secondary" onclick="closeModal()">Mengerti</button>`
        );
        return;
    }

    openModal(
        'Hapus Bahan',
        `<p style="font-size:.875rem;color:var(--text-secondary)">
            Yakin ingin menghapus bahan <strong>${nama}</strong>?
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

// ── Search debounce ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('input[name="search"]');
    if (!input) return;
    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => filterForm.submit(), 400);
    });

    // ── Auto-buka modal jika ada error validasi dari server ──────────────
    const hasErrors = Object.keys(SERVER_ERRORS).length > 0;
    if (hasErrors) {
        // Tentukan modal mana yang harus dibuka berdasarkan OLD_INPUT
        // Jika tidak ada id di old input → berarti ini dari create
        openCreateModal();
        setTimeout(() => {
            setInputValue('newNama',    OLD_INPUT.nama    ?? '');
            setInputValue('newKategori', OLD_INPUT.kategori ?? '');
            setInputValue('newExpired',  OLD_INPUT.expired_expectancy_day ?? '');
            showInlineError('createErrors', SERVER_ERRORS);
        }, 50);
    }
});
</script>
@endpush