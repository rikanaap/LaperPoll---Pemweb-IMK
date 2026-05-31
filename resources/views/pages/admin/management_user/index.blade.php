@extends('layouts.admin')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('breadcrumb', 'Admin / User')

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
        <h1>Daftar User</h1>
        <p>Kelola semua pengguna yang terdaftar</p>
    </div>
    <button class="btn btn--primary" onclick="openCreateModal()">
        <span class="material-icons-round">person_add</span>
        Tambah User
    </button>
</div>

{{-- ── Table Card ────────────────────────────────────────────── --}}
<div class="card">

    {{-- Filter Bar --}}
    <div class="card__header">
        <form method="GET" action="{{ route('admin.user.index') }}" class="filter-bar" id="filterForm">

            <div class="filter-bar__search">
                <span class="material-icons-round">search</span>
                <input
                    type="text"
                    name="search"
                    placeholder="Cari nama atau email..."
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
            </div>

            <select name="verif" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Status</option>
                <option value="verified"   {{ request('verif') === 'verified'   ? 'selected' : '' }}>Verified</option>
                <option value="unverified" {{ request('verif') === 'unverified' ? 'selected' : '' }}>Unverified</option>
            </select>

            <select name="role" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>User</option>
            </select>

            <button type="submit" style="display:none">Cari</button>

            @if(request()->hasAny(['search', 'verif', 'role']))
                <a href="{{ route('admin.user.index') }}" class="btn btn--secondary btn--sm">
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
                    <th>User</th>
                    <th>Role</th>
                    <th>Verifikasi</th>
                    <th>Resep</th>
                    <th>Followers</th>
                    <th>Bergabung</th>
                    <th style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $isSelf = $user->id === auth()->id();
                @endphp
                <tr>
                    <td class="td-sub">{{ $users->firstItem() + $loop->index }}</td>

                    {{-- User --}}
                    <td>
                        <div class="td-user">
                            <div class="td-avatar">
                                @if($user->profile_photo)
                                    <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="td-name">
                                    {{ $user->name }}
                                    @if($isSelf)
                                        <span class="badge badge--orange" style="margin-left:.3rem;font-size:.6rem">Anda</span>
                                    @endif
                                </div>
                                <div class="td-sub">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Role --}}
                    <td>
                        <span class="badge {{ $user->is_admin ? 'badge--purple' : 'badge--gray' }}">
                            <span class="material-icons-round">{{ $user->is_admin ? 'shield' : 'person' }}</span>
                            {{ $user->is_admin ? 'Admin' : 'User' }}
                        </span>
                    </td>

                    {{-- Verifikasi --}}
                    <td>
                        <span class="badge {{ $user->email_verified_at ? 'badge--green' : 'badge--orange' }}">
                            <span class="material-icons-round">
                                {{ $user->email_verified_at ? 'verified' : 'schedule' }}
                            </span>
                            {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                        </span>
                    </td>

                    {{-- Stats --}}
                    <td>{{ number_format($user->reseps_count) }}</td>
                    <td>{{ number_format($user->followers_count) }}</td>

                    {{-- Tanggal --}}
                    <td class="td-sub">{{ $user->created_at->format('d M Y') }}</td>

                    {{-- Aksi --}}
                    <td>
                        <div class="td-actions">

                            {{-- Cek Verifikasi --}}
                            <button
                                class="icon-btn"
                                title="Cek Verifikasi"
                                onclick="openVerifModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                            >
                                <span class="material-icons-round">fact_check</span>
                            </button>

                            {{-- Edit --}}
                            <button
                                class="icon-btn"
                                title="Edit User"
                                onclick="openEditModal(
                                    {{ $user->id }},
                                    '{{ addslashes($user->name) }}',
                                    '{{ $user->email }}',
                                    {{ $user->is_admin ? 'true' : 'false' }}
                                )"
                            >
                                <span class="material-icons-round">edit</span>
                            </button>

                            {{-- Hapus --}}
                            @if(! $isSelf)
                                <button
                                    class="icon-btn icon-btn--danger"
                                    title="Hapus User"
                                    onclick="confirmDelete(
                                        '{{ route('admin.user.destroy', $user) }}',
                                        '{{ addslashes($user->name) }}'
                                    )"
                                >
                                    <span class="material-icons-round">delete</span>
                                </button>
                            @else
                                <button
                                    class="icon-btn"
                                    disabled
                                    title="Tidak bisa hapus akun sendiri"
                                    style="opacity:.3;cursor:not-allowed"
                                >
                                    <span class="material-icons-round">delete</span>
                                </button>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <span class="material-icons-round">group</span>
                            <h3>Belum ada user</h3>
                            <p>
                                @if(request()->hasAny(['search', 'verif', 'role']))
                                    Tidak ada user yang cocok dengan filter yang dipilih.
                                @else
                                    User yang mendaftar akan muncul di sini.
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
    @if($users->hasPages())
    <div class="pagination">
        <span class="pagination__info">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
            dari {{ number_format($users->total()) }} user
        </span>
        <div class="pagination__btns">

            <button
                class="pagination__btn"
                {{ $users->onFirstPage() ? 'disabled' : '' }}
                onclick="window.location='{{ $users->previousPageUrl() }}'"
            >
                <span class="material-icons-round">chevron_left</span>
            </button>

            @php
                $currentPage = $users->currentPage();
                $lastPage    = $users->lastPage();
                $start       = max(1, $currentPage - 3);
                $end         = min($lastPage, $currentPage + 3);
            @endphp

            @if($start > 1)
                <button class="pagination__btn" onclick="window.location='{{ $users->url(1) }}'">1</button>
                @if($start > 2)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
            @endif

            @for($i = $start; $i <= $end; $i++)
                <button
                    class="pagination__btn {{ $currentPage === $i ? 'is-active' : '' }}"
                    onclick="window.location='{{ $users->url($i) }}'"
                >{{ $i }}</button>
            @endfor

            @if($end < $lastPage)
                @if($end < $lastPage - 1)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
                <button class="pagination__btn" onclick="window.location='{{ $users->url($lastPage) }}'">
                    {{ $lastPage }}
                </button>
            @endif

            <button
                class="pagination__btn"
                {{ ! $users->hasMorePages() ? 'disabled' : '' }}
                onclick="window.location='{{ $users->nextPageUrl() }}'"
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

const VERIF_DATA = @json($verifData);

function fmt(n) {
    return Number(n ?? 0).toLocaleString('id-ID');
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function openVerifModal(userId, userName) {
    const d = VERIF_DATA[userId] ?? {};

    const checks = [
        {
            label : 'Total Resep',
            icon  : 'menu_book',
            pass  : d.pass_resep,
            value : `${fmt(d.resep_count)} / ${fmt(d.min_resep)}`,
        },
        {
            label : 'Total Simpan & Favorit',
            icon  : 'bookmark',
            pass  : d.pass_favorit,
            value : `${fmt(d.favorit_count)} / ${fmt(d.min_favorit)}`,
        },
        {
            label : 'Total Followers',
            icon  : 'group',
            pass  : d.pass_followers,
            value : `${fmt(d.followers_count)} / ${fmt(d.min_followers)}`,
        },
        {
            label : 'Total Views Resep',
            icon  : 'visibility',
            pass  : d.pass_views,
            value : `${fmt(d.views_count)} / ${fmt(d.min_views)}`,
        },
    ];

    const allPass = checks.every(c => c.pass);

    const body = `
        <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:.9rem;font-weight:500">
            Pengecekan persyaratan verifikasi untuk
            <strong style="color:var(--text-primary)">${userName}</strong>
        </p>
        <div class="verif-checklist">
            ${checks.map(c => `
                <div class="verif-item">
                    <div class="verif-item__icon verif-item__icon--${c.pass ? 'pass' : 'fail'}">
                        <span class="material-icons-round">${c.pass ? 'check' : 'close'}</span>
                    </div>
                    <span class="verif-item__label">${c.label}</span>
                    <span class="verif-item__value" style="color:${c.pass ? 'var(--green)' : 'var(--red)'}">
                        ${c.value}
                    </span>
                </div>
            `).join('')}
        </div>
        <p style="font-size:.72rem;color:var(--text-muted);margin-top:.9rem;font-weight:500;text-align:center">
            ${allPass
                ? '<span style="color:var(--green)">✓ Semua syarat terpenuhi. Siap diverifikasi.</span>'
                : 'Semua syarat harus terpenuhi sebelum bisa diverifikasi.'
            }
        </p>
    `;

    const footer = `
        <button class="btn btn--secondary" onclick="closeModal()">Tutup</button>
        ${allPass
            ? `<form method="POST" action="/admin/user/${userId}/verify" style="display:contents">
                   <input type="hidden" name="_token"  value="{{ csrf_token() }}">
                   <input type="hidden" name="_method" value="PATCH">
                   <button type="submit" class="btn btn--success">
                       <span class="material-icons-round">verified</span>
                       Verifikasi Sekarang
                   </button>
               </form>`
            : `<button class="btn btn--secondary" disabled style="opacity:.45;cursor:not-allowed">
                   <span class="material-icons-round">block</span>
                   Belum Memenuhi Syarat
               </button>`
        }
    `;

    openModal('Cek Verifikasi User', body, footer);
}

function openCreateModal() {
    openModal(
        'Tambah User Baru',
        `<div class="form-group">
            <label class="form-label">Nama Lengkap <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" placeholder="Nama lengkap" id="newName">
        </div>
        <div class="form-group">
            <label class="form-label">Email <span style="color:var(--red)">*</span></label>
            <input type="email" class="form-control" placeholder="email@example.com" id="newEmail">
        </div>
        <div class="form-group">
            <label class="form-label">Password <span style="color:var(--red)">*</span></label>
            <input type="password" class="form-control" placeholder="Min. 8 karakter" id="newPassword">
            <span class="form-hint">Minimal 8 karakter.</span>
        </div>
        <div class="form-group">
            <label class="form-label">Role</label>
            <select class="form-control" id="newRole">
                <option value="0">User</option>
                <option value="1">Admin</option>
            </select>
        </div>`,
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
        <button class="btn btn--primary" onclick="submitCreate()">
            <span class="material-icons-round">person_add</span> Simpan
        </button>`
    );
}

function openEditModal(id, name, email, isAdmin) {
    openModal(
        'Edit User',
        `<div class="form-group">
            <label class="form-label">Nama Lengkap <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" value="${escHtml(name)}" id="editName">
        </div>
        <div class="form-group">
            <label class="form-label">Email <span style="color:var(--red)">*</span></label>
            <input type="email" class="form-control" value="${escHtml(email)}" id="editEmail">
        </div>
        <div class="form-group">
            <label class="form-label">
                Password Baru
                <span style="color:var(--text-muted);font-weight:500">(kosongkan jika tidak diubah)</span>
            </label>
            <input type="password" class="form-control" placeholder="Password baru" id="editPassword">
        </div>
        <div class="form-group">
            <label class="form-label">Role</label>
            <select class="form-control" id="editRole">
                <option value="0" ${!isAdmin ? 'selected' : ''}>User</option>
                <option value="1" ${isAdmin  ? 'selected' : ''}>Admin</option>
            </select>
        </div>`,
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
        <button class="btn btn--primary" onclick="submitEdit(${id})">
            <span class="material-icons-round">save</span> Simpan
        </button>`
    );
}

function submitCreate() {
    const name     = document.getElementById('newName').value.trim();
    const email    = document.getElementById('newEmail').value.trim();
    const password = document.getElementById('newPassword').value;
    const isAdmin  = document.getElementById('newRole').value;

    if (!name || !email || !password) {
        alert('Nama, email, dan password wajib diisi.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.user.store") }}';
    form.innerHTML = `
        <input type="hidden" name="_token"   value="{{ csrf_token() }}">
        <input type="hidden" name="name"     value="${escHtml(name)}">
        <input type="hidden" name="email"    value="${escHtml(email)}">
        <input type="hidden" name="password" value="${escHtml(password)}">
        <input type="hidden" name="is_admin" value="${isAdmin}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function submitEdit(id) {
    const name     = document.getElementById('editName').value.trim();
    const email    = document.getElementById('editEmail').value.trim();
    const password = document.getElementById('editPassword').value;
    const isAdmin  = document.getElementById('editRole').value;

    if (!name || !email) {
        alert('Nama dan email wajib diisi.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/user/${id}`;
    form.innerHTML = `
        <input type="hidden" name="_token"   value="{{ csrf_token() }}">
        <input type="hidden" name="_method"  value="PATCH">
        <input type="hidden" name="name"     value="${escHtml(name)}">
        <input type="hidden" name="email"    value="${escHtml(email)}">
        <input type="hidden" name="password" value="${escHtml(password)}">
        <input type="hidden" name="is_admin" value="${isAdmin}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function confirmDelete(url, name) {
    if (!confirm(`Hapus user "${name}"?\nSemua data terkait akan ikut terhapus.`)) return;

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