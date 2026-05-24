@extends('layouts.app')

@section('title', 'Pilih Resep - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/pilih-resep.css') }}">
@endpush

@section('content')
<main class="pr-page">

    <x-navbar :backUrl="route('meal-planner.index')"></x-navbar>

    {{-- HEADER --}}
    <div class="pr-header">
        <h1 class="pr-title font-jakarta font-bold">Pilih Resep</h1>
        <div class="pr-slot-info" id="slotLabel"></div>
        <div class="pr-warning font-jakarta font-medium" id="slotWarning" style="display:none;">
            <span class="material-icons-round">warning_amber</span>
            Buka halaman ini dari Meal Planner ya!
        </div>
    </div>

    {{-- SEARCH --}}
    <div class="pr-search-wrap">
        <div class="input">
            <span class="material-icons-round">search</span>
            <input type="text" class="input-data font-jakarta text-body"
                   placeholder="Cari nama resep..." id="searchResep" autocomplete="off">
        </div>
    </div>

    {{-- COUNT --}}
    <div class="pr-count-bar" id="prCountBar" style="display:none;">
        <p class="pr-count-text font-jakarta" id="prCountText"></p>
    </div>

    {{-- LIST --}}
    <section class="resep-menus" id="resepList"></section>

</main>

{{-- LOADING OVERLAY --}}
<div class="pr-loading-overlay" id="loadingOverlay" style="display:none;">
    <div class="pr-spinner"></div>
    <p class="font-jakarta font-semibold">Menyimpan resep...</p>
</div>
@endsection

@push('scripts')
<script>
    window.MP = {
        mealPlannerUrl : "{{ route('meal-planner.index') }}",
        apiBase        : "{{ url('/api/meal-planner') }}",
        csrf           : "{{ csrf_token() }}",
    };
    window.resepData = {!! $reseps->map(fn($r) => [
        'id'            => $r->id,
        'nama'          => $r->title,
        'kalori'        => (int)($r->calorie ?? 0),
        'cook_duration' => $r->cook_duration,
        'thumbnail'     => $r->thumbnail ? asset('storage/'.$r->thumbnail) : null,
    ])->toJson() !!};
</script>
<script src="{{ asset('js/pilih-resep.js') }}"></script>
@endpush