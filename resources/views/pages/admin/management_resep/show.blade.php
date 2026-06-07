@extends('layouts.admin')

@section('title', 'Detail Resep — ' . $resep->title)
@section('page-title', 'Detail Resep')
@section('breadcrumb', 'Admin / Resep / ' . Str::limit($resep->title, 30))

@section('content')

<x-admin.alert />

<div class="page-header">
    <div class="page-header__left">
        <h1>{{ $resep->title }}</h1>
        <p>Detail lengkap resep beserta bahan, langkah, dan feedback</p>
    </div>
    <div class="page-header__actions">
        <form method="POST" action="{{ route('admin.resep.togglePublish', $resep) }}" style="display:contents">
            @csrf @method('PATCH')
            <button type="submit" class="btn {{ $resep->is_published ? 'btn--secondary' : 'btn--success' }}">
                <span class="material-icons-round">
                    {{ $resep->is_published ? 'unpublished' : 'publish' }}
                </span>
                {{ $resep->is_published ? 'Unpublish' : 'Publish' }}
            </button>
        </form>

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

    {{-- Info Utama --}}
    <div class="card">
        <div class="card__header">
            <div class="card__title">Informasi Resep</div>
        </div>
        <div class="card__body">

            @if($resep->thumbnail)
                <img
                    src="{{ $resep->thumbnail_url }}"
                    alt="{{ $resep->title }}"
                    class="resep-detail-thumb"
                >
            @endif

            <div class="detail-meta">
                @php
                    $metas = [
                        ['label' => 'Author',      'value' => $resep->user?->name ?? '—'],
                        ['label' => 'Kategori',     'value' => $resep->mainFilter?->title ?? '—'],
                        ['label' => 'Durasi Masak', 'value' => $resep->cook_duration_formatted],
                        ['label' => 'Kalori',       'value' => $resep->calorie ? number_format($resep->calorie) . ' kkal' : '—'],
                        ['label' => 'Rating',       'value' => number_format($resep->current_star, 1) . ' / 5'],
                        ['label' => 'Total Views',  'value' => number_format($resep->views_count)],
                        ['label' => 'Dibuat',       'value' => $resep->created_at->format('d M Y, H:i')],
                    ];
                @endphp

                @foreach($metas as $meta)
                    <div class="detail-meta__row">
                        <span class="detail-meta__label">{{ $meta['label'] }}</span>
                        <span class="detail-meta__value">{{ $meta['value'] }}</span>
                    </div>
                @endforeach

                <div class="detail-meta__row">
                    <span class="detail-meta__label">Status</span>
                    <span class="badge {{ $resep->is_published ? 'badge--green' : 'badge--gray' }}">
                        {{ $resep->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
            </div>

            @if($resep->description)
                <div class="detail-section">
                    <div class="detail-section__label">DESKRIPSI</div>
                    <p class="detail-section__text">{{ $resep->description }}</p>
                </div>
            @endif

            @if($resep->filters->isNotEmpty())
                <div class="detail-section">
                    <div class="detail-section__label">TAGS / FILTER</div>
                    <div class="badge-group">
                        @foreach($resep->filters as $filter)
                            <span class="badge badge--blue">{{ $filter->title }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Bahan-Bahan --}}
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
                        <td><span class="td-name">{{ $bahan->nama }}</span></td>
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

{{-- Langkah Memasak --}}
<div class="card mb-4">
    <div class="card__header">
        <div>
            <div class="card__title">Langkah Memasak</div>
            <div class="card__subtitle">{{ $resep->langkahs->count() }} langkah</div>
        </div>
    </div>
    <div class="card__body">
        @forelse($resep->langkahs->sortBy('step_order') as $langkah)
        <div class="step-card">
            <div class="step-card__number">{{ $langkah->step_order }}</div>
            <div class="step-card__content">
                <p class="step-card__desc">{{ $langkah->description }}</p>
                <div class="step-card__meta">
                    @if($langkah->step_duration)
                        <span class="badge badge--orange">
                            <span class="material-icons-round">timer</span>
                            {{ $langkah->step_duration }}
                        </span>
                    @endif
                    @foreach($langkah->langkahBahans as $lb)
                        <span class="badge badge--blue">
                            {{ $lb->resepBahan->bahan->nama ?? '—' }}
                            ({{ number_format($lb->gram_total) }}g)
                        </span>
                    @endforeach
                </div>
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

{{-- Feedback --}}
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
                    <td class="td-desc">
                        {{ $feedback->description ? Str::limit($feedback->description, 80) : '—' }}
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