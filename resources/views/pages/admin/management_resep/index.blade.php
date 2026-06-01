@extends('layouts.admin')

@section('title', 'Manajemen Resep')
@section('page-title', 'Manajemen Resep')
@section('breadcrumb', 'Admin / Resep')

@section('content')

<x-admin.alert />

<div class="page-header">
    <div class="page-header__left">
        <h1>Daftar Resep</h1>
        <p>Kelola semua resep yang ada di platform</p>
    </div>
</div>

<div class="card">

    <div class="card__header">
        <form method="GET" action="{{ route('admin.resep.index') }}" class="filter-bar" id="resepForm">

            <div class="filter-bar__search">
                <span class="material-icons-round">search</span>
                <input
                    type="text"
                    name="search"
                    placeholder="Cari judul resep..."
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
            </div>

            <select name="status" onchange="resepForm.submit()">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Draft</option>
            </select>

            <select name="filter_id" onchange="resepForm.submit()">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ request('filter_id') == $kat->id ? 'selected' : '' }}>
                        {{ $kat->title }}
                    </option>
                @endforeach
            </select>

            <button type="submit" hidden>Cari</button>

            @if(request()->hasAny(['search', 'status', 'filter_id']))
                <a href="{{ route('admin.resep.index') }}" class="btn btn--secondary btn--sm">
                    <span class="material-icons-round">close</span> Reset
                </a>
            @endif

        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Resep</th>
                    <th>Author</th>
                    <th>Kategori</th>
                    <th>Rating</th>
                    <th>Views</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reseps as $resep)
                <tr>
                    <td class="td-sub">{{ $reseps->firstItem() + $loop->index }}</td>

                    <td>
                        <div class="td-user">
                            @if($resep->thumbnail)
                                <img
                                    src="{{ $resep->thumbnail_url }}"
                                    class="resep-thumb"
                                    alt="{{ $resep->title }}"
                                    loading="lazy"
                                >
                            @else
                                <div class="resep-thumb resep-thumb--placeholder">
                                    <span class="material-icons-round">image</span>
                                </div>
                            @endif
                            <div>
                                <div class="td-name">{{ Str::limit($resep->title, 30) }}</div>
                                <div class="td-sub">
                                    {{ $resep->cook_duration }}
                                    {{ $resep->calorie ? '· ' . number_format($resep->calorie) . ' kkal' : '' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="td-user">
                            <div class="td-avatar">
                                @if($resep->user?->profile_photo)
                                    <img src="{{ Storage::url($resep->user->profile_photo) }}" alt="">
                                @else
                                    {{ strtoupper(substr($resep->user?->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <span class="td-name">{{ $resep->user?->name ?? '—' }}</span>
                        </div>
                    </td>

                    <td>
                        @if($resep->mainFilter)
                            <span class="badge badge--blue">{{ $resep->mainFilter->title }}</span>
                        @else
                            <span class="td-sub">—</span>
                        @endif
                    </td>

                    <td>
                        <div class="star-display">
                            <span class="material-icons-round">star</span>
                            {{ number_format($resep->current_star, 1) }}
                        </div>
                    </td>

                    <td>{{ number_format($resep->views_count) }}</td>

                    <td>
                        <span class="badge {{ $resep->is_published ? 'badge--green' : 'badge--gray' }}">
                            <span class="material-icons-round">
                                {{ $resep->is_published ? 'check_circle' : 'unpublished' }}
                            </span>
                            {{ $resep->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>

                    <td class="td-sub">{{ $resep->created_at->format('d M Y') }}</td>

                    <td>
                        <div class="td-actions">
                            <a href="{{ route('admin.resep.show', $resep) }}" class="icon-btn" title="Detail">
                                <span class="material-icons-round">visibility</span>
                            </a>
                            <form method="POST" action="{{ route('admin.resep.togglePublish', $resep) }}" style="display:contents">
                                @csrf @method('PATCH')
                                <button
                                    type="submit"
                                    class="icon-btn icon-btn--success"
                                    title="{{ $resep->is_published ? 'Unpublish' : 'Publish' }}"
                                >
                                    <span class="material-icons-round">
                                        {{ $resep->is_published ? 'unpublished' : 'publish' }}
                                    </span>
                                </button>
                            </form>
                            <button
                                class="icon-btn icon-btn--danger"
                                title="Hapus"
                                onclick="resepDelete('{{ route('admin.resep.destroy', $resep) }}', '{{ addslashes($resep->title) }}')"
                            >
                                <span class="material-icons-round">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <span class="material-icons-round">menu_book</span>
                            <h3>Belum ada resep</h3>
                            <p>
                                {{ request()->hasAny(['search', 'status', 'filter_id'])
                                    ? 'Tidak ada resep yang cocok dengan pencarian.'
                                    : 'Resep yang ditambahkan user akan muncul di sini.' }}
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reseps->hasPages())
    <div class="pagination">
        <span class="pagination__info">
            Menampilkan {{ $reseps->firstItem() }}–{{ $reseps->lastItem() }}
            dari {{ number_format($reseps->total()) }} resep
        </span>
        <div class="pagination__btns">

            <button
                class="pagination__btn"
                {{ $reseps->onFirstPage() ? 'disabled' : '' }}
                onclick="window.location='{{ $reseps->previousPageUrl() }}'"
            >
                <span class="material-icons-round">chevron_left</span>
            </button>

            @php
                $cp = $reseps->currentPage();
                $lp = $reseps->lastPage();
                $s  = max(1, $cp - 3);
                $e  = min($lp, $cp + 3);
            @endphp

            @if($s > 1)
                <button class="pagination__btn" onclick="window.location='{{ $reseps->url(1) }}'">1</button>
                @if($s > 2)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
            @endif

            @for($i = $s; $i <= $e; $i++)
                <button
                    class="pagination__btn {{ $cp === $i ? 'is-active' : '' }}"
                    onclick="window.location='{{ $reseps->url($i) }}'"
                >{{ $i }}</button>
            @endfor

            @if($e < $lp)
                @if($e < $lp - 1)
                    <span class="pagination__btn" style="pointer-events:none">…</span>
                @endif
                <button class="pagination__btn" onclick="window.location='{{ $reseps->url($lp) }}'">{{ $lp }}</button>
            @endif

            <button
                class="pagination__btn"
                {{ ! $reseps->hasMorePages() ? 'disabled' : '' }}
                onclick="window.location='{{ $reseps->nextPageUrl() }}'"
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
function resepDelete(url, name) {
    if (!confirm(`Hapus resep "${name}"?\nTindakan ini tidak dapat dibatalkan.`)) return;
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = url;
    f.innerHTML = `
        <input type="hidden" name="_token"  value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(f);
    f.submit();
}

document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('#resepForm input[name="search"]');
    if (!input) return;
    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => resepForm.submit(), 400);
    });
});
</script>
@endpush