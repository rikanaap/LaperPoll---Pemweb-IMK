@extends('layouts.admin')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('breadcrumb', 'Admin / User')

@section('content')

<x-admin.alert />

<div class="page-header">
    <div class="page-header__left">
        <h1>Daftar User</h1>
        <p>Kelola semua pengguna yang terdaftar</p>
    </div>
</div>

<div class="card">

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

            <select name="verif" onchange="filterForm.submit()">
                <option value="">Semua Status</option>
                <option value="verified"   {{ request('verif') === 'verified'   ? 'selected' : '' }}>Verified</option>
                <option value="unverified" {{ request('verif') === 'unverified' ? 'selected' : '' }}>Unverified</option>
            </select>

            <select name="role" onchange="filterForm.submit()">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>User</option>
            </select>

            <button type="submit" hidden>Cari</button>

            @if(request()->hasAny(['search', 'verif', 'role']))
                <a href="{{ route('admin.user.index') }}" class="btn btn--secondary btn--sm">
                    <span class="material-icons-round">close</span>
                    Reset
                </a>
            @endif

        </form>
    </div>

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
                @php $isSelf = $user->id === auth()->id(); @endphp
                <tr>
                    <td class="td-sub">{{ $users->firstItem() + $loop->index }}</td>

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
                                        <span class="badge badge--orange badge--xs">Anda</span>
                                    @endif
                                </div>
                                <div class="td-sub">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="badge {{ $user->is_admin ? 'badge--purple' : 'badge--gray' }}">
                            <span class="material-icons-round">{{ $user->is_admin ? 'shield' : 'person' }}</span>
                            {{ $user->is_admin ? 'Admin' : 'User' }}
                        </span>
                    </td>

                    <td>
                        <span class="badge {{ $user->email_verified_at ? 'badge--green' : 'badge--orange' }}">
                            <span class="material-icons-round">
                                {{ $user->email_verified_at ? 'verified' : 'schedule' }}
                            </span>
                            {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                        </span>
                    </td>

                    <td>{{ number_format($user->reseps_count) }}</td>
                    <td>{{ number_format($user->followers_count) }}</td>

                    <td class="td-sub">{{ $user->created_at->format('d M Y') }}</td>

                    <td>
                        <div class="td-actions">

                            <button
                                class="icon-btn"
                                title="Cek Verifikasi"
                                onclick="openVerifModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                            >
                                <span class="material-icons-round">fact_check</span>
                            </button>

                            <button
                                class="icon-btn"
                                title="Edit User"
                                onclick="openEditModal(
                                    {{ $user->id }},
                                    '{{ addslashes($user->name) }}',
                                    '{{ $user->email }}',
                                    {{ $user->is_admin ? 'true' : 'false' }},
                                    '{{ route('admin.user.update', $user) }}'
                                )"
                            >
                                <span class="material-icons-round">edit</span>
                            </button>

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
                                <button class="icon-btn" disabled title="Tidak bisa hapus akun sendiri">
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
                                {{ request()->hasAny(['search', 'verif', 'role'])
                                    ? 'Tidak ada user yang cocok dengan filter yang dipilih.'
                                    : 'User yang mendaftar akan muncul di sini.' }}
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
                $cp = $users->currentPage();
                $lp = $users->lastPage();
                $s  = max(1, $cp - 3);
                $e  = min($lp, $cp + 3);
            @endphp

            @if($s > 1)
                <button class="pagination__btn" onclick="window.location='{{ $users->url(1) }}'">1</button>
                @if($s > 2)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
            @endif

            @for($i = $s; $i <= $e; $i++)
                <button
                    class="pagination__btn {{ $cp === $i ? 'is-active' : '' }}"
                    onclick="window.location='{{ $users->url($i) }}'"
                >{{ $i }}</button>
            @endfor

            @if($e < $lp)
                @if($e < $lp - 1)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
                <button class="pagination__btn" onclick="window.location='{{ $users->url($lp) }}'">{{ $lp }}</button>
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
const CSRF_TOKEN = '{{ csrf_token() }}';

const VERIF_ROUTES = @json(
    $users->getCollection()->mapWithKeys(fn ($u) => [
        $u->id => route('admin.user.verify', $u)
    ])->toArray()
);

function fmt(n) {
    return Number(n ?? 0).toLocaleString('id-ID');
}

function setInputValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value;
}

function submitForm(action, method, fields) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    const addHidden = (name, value) => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = name;
        input.value = value;
        form.appendChild(input);
    };

    addHidden('_token', CSRF_TOKEN);
    if (method !== 'POST') addHidden('_method', method);
    Object.entries(fields).forEach(([k, v]) => addHidden(k, v));

    document.body.appendChild(form);
    form.submit();
}

// ── Verif Modal ───────────────────────────────────────────
function openVerifModal(userId, userName) {
    const d = VERIF_DATA[userId] ?? {};

    const checks = [
        { label: 'Total Resep',            pass: d.pass_resep,     value: `${fmt(d.resep_count)} / ${fmt(d.min_resep)}`         },
        { label: 'Total Simpan & Favorit', pass: d.pass_favorit,   value: `${fmt(d.favorit_count)} / ${fmt(d.min_favorit)}`     },
        { label: 'Total Followers',        pass: d.pass_followers, value: `${fmt(d.followers_count)} / ${fmt(d.min_followers)}` },
        { label: 'Total Views Resep',      pass: d.pass_views,     value: `${fmt(d.views_count)} / ${fmt(d.min_views)}`         },
    ];

    const allPass = checks.every(c => c.pass);

    const checksHtml = checks.map(c => `
        <div class="verif-item">
            <div class="verif-item__icon verif-item__icon--${c.pass ? 'pass' : 'fail'}">
                <span class="material-icons-round">${c.pass ? 'check' : 'close'}</span>
            </div>
            <span class="verif-item__label">${c.label}</span>
            <span class="verif-item__value verif-item__value--${c.pass ? 'pass' : 'fail'}">${c.value}</span>
        </div>
    `).join('');

    const body = `
        <p class="verif-intro">
            Pengecekan persyaratan verifikasi untuk
            <strong>${userName}</strong>
        </p>
        <div class="verif-checklist">${checksHtml}</div>
        <p class="verif-summary verif-summary--${allPass ? 'pass' : 'fail'}">
            ${allPass
                ? '✓ Semua syarat terpenuhi. Siap diverifikasi.'
                : 'Semua syarat harus terpenuhi sebelum bisa diverifikasi.'}
        </p>
    `;

    const footer = `
        <button class="btn btn--secondary" onclick="closeModal()">Tutup</button>
        ${allPass
            ? `<button class="btn btn--success" onclick="submitVerif(${userId})">
                   <span class="material-icons-round">verified</span>
                   Verifikasi Sekarang
               </button>`
            : `<button class="btn btn--secondary" disabled>
                   <span class="material-icons-round">block</span>
                   Belum Memenuhi Syarat
               </button>`
        }
    `;

    openModal('Cek Verifikasi User', body, footer);
}

function submitVerif(userId) {
    submitForm(VERIF_ROUTES[userId], 'PATCH', {});
}

// ── Edit Modal ────────────────────────────────────────────
function openEditModal(id, name, email, isAdmin, route) {
    openModal(
        'Edit User',
        `<div class="form-group">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editName">
        </div>
        <div class="form-group">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="editEmail">
        </div>
        <div class="form-group">
            <label class="form-label">
                Password Baru
                <span class="text-muted">(kosongkan jika tidak diubah)</span>
            </label>
            <input type="password" class="form-control" id="editPassword" placeholder="Password baru">
        </div>
        <div class="form-group">
            <label class="form-label">Role</label>
            <select class="form-control" id="editRole">
                <option value="0">User</option>
                <option value="1">Admin</option>
            </select>
        </div>`,
        `<button class="btn btn--secondary" onclick="closeModal()">Batal</button>
         <button class="btn btn--primary" onclick="submitEdit('${route}')">
             <span class="material-icons-round">save</span> Simpan
         </button>`
    );

    setInputValue('editName', name);
    setInputValue('editEmail', email);
    document.getElementById('editRole').value = isAdmin ? '1' : '0';
}

function submitEdit(route) {
    const name     = document.getElementById('editName').value.trim();
    const email    = document.getElementById('editEmail').value.trim();
    const password = document.getElementById('editPassword').value;
    const isAdmin  = document.getElementById('editRole').value;

    if (!name || !email) {
        alert('Nama dan email wajib diisi.');
        return;
    }

    const fields = { name, email, is_admin: isAdmin };
    if (password) fields.password = password;

    submitForm(route, 'PATCH', fields);
}

// ── Delete ────────────────────────────────────────────────
function confirmDelete(url, name) {
    if (!confirm(`Hapus user "${name}"?\nSemua data terkait akan ikut terhapus.`)) return;
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