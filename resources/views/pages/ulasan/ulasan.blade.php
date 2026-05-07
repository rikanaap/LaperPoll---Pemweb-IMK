@extends('layouts.app')

@section('title', 'Detail Resep - LaperPoll')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pages/ulasan.css') }}">
@endpush

<body>
    <main class="review-main-card">
        <div class="review-image-zone">
                <div class="image-frame">
                    <img src="https://images.unsplash.com/photo-1525351484163-7529414344d8?q=80&w=600" alt="Preview Masakan" class="recipe-img-preview">
                </div>
            </div>
        <div class="review-content-flex">
            <div class="review-form-zone">
                <div class="rating-group">
                    <h2 class="info-label">
                        <span class="material-icons-round">star</span> Rating Resep
                    </h2>
                    <div class="stars-picker" id="starsPicker">
                        <span class="material-icons-round star-icon" data-value="1">star_outline</span>
                        <span class="material-icons-round star-icon" data-value="2">star_outline</span>
                        <span class="material-icons-round star-icon" data-value="3">star_outline</span>
                        <span class="material-icons-round star-icon" data-value="4">star_outline</span>
                        <span class="material-icons-round star-icon" data-value="5">star_outline</span>
                    </div>
                </div>    
                <div class="comment-group">
                    <h2 class="info-label">
                        <span class="material-icons-round">rate_review</span> Tulis Ulasan
                    </h2>
                    <div class="textarea-wrapper">
                        <textarea id="reviewText" placeholder="Bagaimana pengalaman memasak Anda dengan resep ini?"></textarea>
                        <button class="btn-upload-img">
                            <span class="material-icons-round">add_a_photo</span>
                        </button>
                    </div>
                </div>
                <div class="action-zone">
                    <button id="btn-submit-review" class="btn-primary-desktop" onclick="submitReview()">
                        KIRIM ULASAN
                    </button>
                    <button class="btn-outline-desktop" onclick= "window.location.href='/detail-resep'">
                        LEWATI
                    </button>
                </div>
            </div>  
        </div>
    </main>
    
    <script src="../js/ulasan.js"></script>
</body>
</html>