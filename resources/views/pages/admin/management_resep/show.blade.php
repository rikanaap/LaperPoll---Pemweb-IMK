@extends('layouts.admin')

@section('title', 'Detail Resep — ' . $resep->title)
@section('page-title', 'Detail Resep')
@section('breadcrumb', 'Admin / Resep / ' . Str::limit($resep->title, 30))

@section('content')

{{-- ── Flash --}}
@if(session('success'))
    <div class="alert alert--success">
        <span class="material-icons-round">check_circle</span>
        {{ session('success') }}
    </div>
@endif

{{-- ── Page Header ───────────────────────────────────────────── --}}
<div class="page-header">
    <div class="page-header__left">
        <h1>{{ $resep->title }}</h1>
        <p>Detail lengkap resep beserta bahan, langkah, dan feedback</p>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center">
        {{-- Toggle Publish --}}
        <form method="POST" action="{{ route('admin.resep.togglePublish', $resep) }}" style="display:contents">
            @csrf @method('PATCH')
            <button
                type="submit"
                class="btn {{ $resep->is_published ? 'btn--secondary' : 'btn--success' }}"
            >
                <span class="material-icons-round">
                    {{ $resep->is_published ? 'unpublished' : 'publish' }}
                </span>
                {{ $resep->is_published ? 'Unpublish' : 'Publish' }}
            </button>
        </form>

        {{-- Hapus --}}
        <button
            class="btn btn--danger"
            onclick="confirmDelete('{{ route('admin.resep.destroy', $resep) }}', '{{ addslashes($resep->title) }}')"
        >
            <span class="material-icons-round">delete</span>
            Hapus Resep
        </button>

        <a href="{{ route('admin.resep.index') }}" class="btn btn--secondary">
            <span class="material-icons-round">arrow_back</span>
            Kembali
        </a>
    </div>
</div>

<div class="grid-2 mb-4">

    {{-- ── Info Utama ──────────────────────────────────────── --}}
    <div class="card">
        <div class="card__header">
            <div class="card__title">Informasi Resep</div>
        </div>
        <div style="padding:1.1rem 1.25rem">

            {{-- Thumbnail --}}
            @if($resep->thumbnail)
                <img
                    src="{{ Storage::url($resep->thumbnail) }}"
                    alt="{{ $resep->title }}"
                    style="width:100%;height:200px;object-fit:cover;border-radius:var(--radius-md);margin-bottom:1rem"
                >
            @endif

            {{-- Meta rows --}}
            @php
                $metas = [
                    ['label' => 'Author',       'value' => $resep->user?->name ?? '—'],
                    ['label' => 'Kategori',      'value' => $resep->mainFilter?->title ?? '—'],
                    ['label' => 'Durasi Masak',  'value' => $resep->cook_duration],
                    ['label' => 'Kalori',        'value' => $resep->calorie ? number_format($resep->calorie) . ' kkal' : '—'],
                    ['label' => 'Rating',        'value' => number_format($resep->current_star, 1) . ' / 5'],
                    ['label' => 'Total Views',   'value' => number_format($resep->views_count)],
                    ['label' => 'Dibuat',        'value' => $resep->created_at->format('d M Y, H:i')],
                ];
            @endphp

            <div style="display:flex;flex-direction:column;gap:.6rem">
                @foreach($metas as $meta)
                <div style="display:flex;justify-content:space-between;font-size:.82rem;padding:.5rem 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-muted);font-weight:600">{{ $meta['label'] }}</span>
                    <span style="font-weight:700;color:var(--text-primary)">{{ $meta['value'] }}</span>
                </div>
                @endforeach

                {{-- Status --}}
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:.82rem;padding:.5rem 0">
                    <span style="color:var(--text-muted);font-weight:600">Status</span>
                    <span class="badge {{ $resep->is_published ? 'badge--green' : 'badge--gray' }}">
                        {{ $resep->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
            </div>

            {{-- Deskripsi --}}
            @if($resep->description)
                <div style="margin-top:1rem">
                    <div style="font-size:.75rem;font-weight:700;color:var(--text-muted);margin-bottom:.4rem">
                        DESKRIPSI
                    </div>
                    <p style="font-size:.82rem;color:var(--text-secondary);line-height:1.6">
                        {{ $resep->description }}
                    </p>
                </div>
            @endif

            {{-- Filter Tags --}}
            @if($resep->filters->isNotEmpty())
                <div style="margin-top:1rem">
                    <div style="font-size:.75rem;font-weight:700;color:var(--text-muted);margin-bottom:.5rem">
                        TAGS / FILTER
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:.4rem">
                        @foreach($resep->filters as $filter)
                            <span class="badge badge--blue">{{ $filter->title }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Bahan-Bahan ──────────────────────────────────────── --}}
    <div class="card">
        <div class="card__header">
            <div>
                <div class="card__title">Bahan-Bahan</div>
                <div class="card__subtitle">{{ $resep->bahans->count() }} bahan digunakan</div>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama Bahan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resep->bahans as $bahan)
                    <tr>
                        <td>
                            <span class="td-name">{{ $bahan->nama }}</span>
                        </td>
                        <td class="td-sub">{{ number_format($bahan->pivot->gram_total) }} gram</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2">
                            <div class="empty-state">
                                <span class="material-icons-round">kitchen</span>
                                <h3>Belum ada bahan</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── Langkah-Langkah ──────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card__header">
        <div>
            <div class="card__title">Langkah Memasak</div>
            <div class="card__subtitle">{{ $resep->langkahs->count() }} langkah</div>
        </div>
    </div>
    <div style="padding:1.1rem 1.25rem;display:flex;flex-direction:column;gap:.75rem">
        @forelse($resep->langkahs->sortBy('step_order') as $langkah)
        <div style="display:flex;gap:1rem;padding:1rem;background:var(--bg-page);border-radius:var(--radius-md);border:1px solid var(--border)">
            <div style="width:32px;height:32px;border-radius:50%;background:var(--orange);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:800;flex-shrink:0">
                {{ $langkah->step_order }}
            </div>
            <div style="flex:1">
                <p style="font-size:.83rem;color:var(--text-primary);line-height:1.6;margin-bottom:.4rem">
                    {{ $langkah->description }}
                </p>
                @if($langkah->step_duration)
                    <span class="badge badge--orange">
                        <span class="material-icons-round">timer</span>
                        {{ $langkah->step_duration }}
                    </span>
                @endif
                @if($langkah->langkahBahans->isNotEmpty())
                    <div style="margin-top:.5rem;display:flex;flex-wrap:wrap;gap:.3rem">
                        @foreach($langkah->langkahBahans as $lb)
                            <span class="badge badge--blue">
                                {{ $lb->resepBahan->bahan->nama ?? '—' }}
                                ({{ number_format($lb->gram_total) }}g)
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state">
            <span class="material-icons-round">format_list_numbered</span>
            <h3>Belum ada langkah</h3>
        </div>
        @endforelse
    </div>
</div>

{{-- ── Feedback ──────────────────────────────────────────────── --}}
<div class="card">
    <div class="card__header">
        <div>
            <div class="card__title">Feedback</div>
            <div class="card__subtitle">{{ $resep->feedbacks->count() }} ulasan diterima</div>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resep->feedbacks as $feedback)
                <tr>
                    <td>
                        <div class="td-user">
                            <div class="td-avatar">
                                @if($feedback->user?->profile_photo)
                                    <img src="{{ Storage::url($feedback->user->profile_photo) }}" alt="">
                                @else
                                    {{ strtoupper(substr($feedback->user?->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="td-name">{{ $feedback->user?->name ?? '—' }}</div>
                                <div class="td-sub">{{ $feedback->user?->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="star-display">
                            <span class="material-icons-round">star</span>
                            {{ number_format($feedback->rating, 1) }}
                        </div>
                    </td>
                    <td style="max-width:280px">
                        <span style="font-size:.8rem;color:var(--text-secondary)">
                            {{ $feedback->description ? Str::limit($feedback->description, 80) : '—' }}
                        </span>
                    </td>
                    <td class="td-sub">{{ $feedback->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <span class="material-icons-round">star_border</span>
                            <h3>Belum ada feedback</h3>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(url, name) {
    if (!confirm(`Hapus resep "${name}"?\nTindakan ini tidak dapat dibatalkan.`)) return;

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
</script>
@endpush