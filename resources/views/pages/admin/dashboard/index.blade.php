@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Admin / Dashboard')

@section('content')

<x-admin.alert />

{{-- Stat Cards --}}
<div class="stat-grid stat-grid--5">

    <div class="stat-card">
        <div class="stat-card__top">
            <div class="stat-card__icon stat-card__icon--orange">
                <span class="material-icons-round">group</span>
            </div>
        </div>
        <div class="stat-card__value">{{ number_format($total_users) }}</div>
        <div class="stat-card__label">Total User</div>
    </div>

    <div class="stat-card">
        <div class="stat-card__top">
            <div class="stat-card__icon stat-card__icon--blue">
                <span class="material-icons-round">menu_book</span>
            </div>
        </div>
        <div class="stat-card__value">{{ number_format($total_resep) }}</div>
        <div class="stat-card__label">Total Resep</div>
    </div>

    <div class="stat-card">
        <div class="stat-card__top">
            <div class="stat-card__icon stat-card__icon--green">
                <span class="material-icons-round">kitchen</span>
            </div>
        </div>
        <div class="stat-card__value">{{ number_format($total_bahan) }}</div>
        <div class="stat-card__label">Total Bahan</div>
    </div>

    <div class="stat-card">
        <div class="stat-card__top">
            <div class="stat-card__icon stat-card__icon--purple">
                <span class="material-icons-round">filter_list</span>
            </div>
        </div>
        <div class="stat-card__value">{{ number_format($total_filter) }}</div>
        <div class="stat-card__label">Total Filter</div>
    </div>

    <div class="stat-card">
        <div class="stat-card__top">
            <div class="stat-card__icon stat-card__icon--blue">
                <span class="material-icons-round">verified</span>
            </div>
        </div>
        <div class="stat-card__value">{{ number_format($verified_users) }}</div>
        <div class="stat-card__label">User Terverifikasi</div>
    </div>

</div>

{{-- Grid: Resep + User Terbaru --}}
<div class="grid-2 mb-4">

    {{-- Resep Terbaru --}}
    <div class="card">
        <div class="card__header">
            <div>
                <div class="card__title">Resep Terbaru</div>
                <div class="card__subtitle">5 resep terakhir ditambahkan</div>
            </div>
            <a href="{{ route('admin.resep.index') }}" class="btn btn--secondary btn--sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Resep</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latest_reseps as $resep)
                    <tr>
                        <td>
                            <div class="td-user">
                                @if($resep->thumbnail)
                                    <img src="{{ Storage::url($resep->thumbnail) }}" class="resep-thumb" alt="{{ $resep->title }}">
                                @else
                                    <div class="resep-thumb resep-thumb--placeholder">
                                        <span class="material-icons-round">image</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="td-name">{{ Str::limit($resep->title, 28) }}</div>
                                    <div class="td-sub">{{ $resep->cook_duration }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="td-sub">{{ $resep->user?->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $resep->is_published ? 'badge--green' : 'badge--gray' }}">
                                {{ $resep->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>
                            <div class="star-display">
                                <span class="material-icons-round">star</span>
                                {{ number_format($resep->current_star, 1) }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <span class="material-icons-round">menu_book</span>
                                <h3>Belum ada resep</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- User Terbaru --}}
    <div class="card">
        <div class="card__header">
            <div>
                <div class="card__title">User Terbaru</div>
                <div class="card__subtitle">5 user terakhir bergabung</div>
            </div>
            <a href="{{ route('admin.user.index') }}" class="btn btn--secondary btn--sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Status</th>
                        <th>Resep</th>
                        <th>Bergabung</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latest_users as $user)
                    <tr>
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
                                    <div class="td-name">{{ $user->name }}</div>
                                    <div class="td-sub">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $user->email_verified_at ? 'badge--green' : 'badge--orange' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                            </span>
                        </td>
                        <td>{{ $user->reseps_count }}</td>
                        <td class="td-sub">{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <span class="material-icons-round">group</span>
                                <h3>Belum ada user</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection