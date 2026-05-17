@extends('layouts.app')

@section('title', 'Main Menu - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/favorit.css') }}">
@endpush

<main class="main-content flex flex-col font-jakarta">
    <x-navbar :back="true"></x-navbar>
    <section class="profile-card">
        <div class="profile-info">
            <img src="https://i.pravatar.cc/150?u=chefmoni" alt="Chef Moni" class="avatar">
            <div class="profile-text">
                <h2>Chef Moni <i class="fa-solid fa-pen edit-icon"></i></h2>
                <p>@masterchefmoni</p>
            </div>
        </div>
        <div class="profile-stats">
            <div class="stat-item"><strong>4</strong><span>Resep</span></div>
            <div class="stat-item"><strong>6</strong><span>Pengikut</span></div>
            <div class="stat-item"><strong>4</strong><span>Mengikuti</span></div>
        </div>
    </section>

    <!-- Judul Halaman -->
    <div class="section-title">
        <i class="fa-solid fa-heart heart-active"></i>
        <h3>Resep Favorit Saya</h3>
        <i class="fa-solid fa-chevron-right arrow"></i>
        <span class="count">3</span>
    </div>

   
    <div class="recipe-grid">
    
    <div class="recipe-card">
        <div class="card-header">
            <div class="chef-info">
                <span class="chef-initial">CM</span>
                <span class="chef-name">Chef Moni</span>
                <i class="fa-solid fa-circle-check verified"></i>
            </div>
        </div>
        <div class="recipe-image-placeholder">
            <i class="fa-solid fa-hamburger placeholder-icon"></i>
            <button class="fav-btn active" onclick="toggleFavorite(this)">
                <i class="fa-solid fa-heart"></i>
            </button>
        </div>
        <div class="card-body">
            <h4>Rendang Daging Sapi</h4>
            <div class="meta-info">
                <span><i class="fa-solid fa-star"></i> 5.0</span>
                <span><i class="fa-regular fa-clock"></i> 90 mnt</span>
            </div>
            <div class="card-footer">
                <span><i class="fa-regular fa-eye"></i> 312</span>
                <i class="fa-regular fa-bookmark bookmark-icon"></i>
            </div>
        </div>
    </div>

    
    <div class="recipe-card">
        <div class="card-header">
            <div class="chef-info">
                <span class="chef-initial">CM</span>
                <span class="chef-name">Chef Moni</span>
                <i class="fa-solid fa-circle-check verified"></i>
            </div>
        </div>
        <div class="recipe-image-placeholder">
            <i class="fa-solid fa-leaf placeholder-icon"></i>
            <button class="fav-btn active" onclick="toggleFavorite(this)">
                <i class="fa-solid fa-heart"></i>
            </button>
        </div>
        <div class="card-body">
            <h4>Gado-Gado Spesial</h4>
            <div class="meta-info">
                <span><i class="fa-solid fa-star"></i> 4.8</span>
                <span><i class="fa-regular fa-clock"></i> 30 mnt</span>
            </div>
            <div class="card-footer">
                <span><i class="fa-regular fa-eye"></i> 189</span>
                <i class="fa-regular fa-bookmark bookmark-icon"></i>
            </div>
        </div>
    </div>

    
    <div class="recipe-card">
        <div class="card-header">
            <div class="chef-info">
                <span class="chef-initial">CM</span>
                <span class="chef-name">Chef Moni</span>
                <i class="fa-solid fa-circle-check verified"></i>
            </div>
        </div>
        <div class="recipe-image-placeholder">
            <i class="fa-solid fa-bowl-rice placeholder-icon"></i>
            <button class="fav-btn active" onclick="toggleFavorite(this)">
                <i class="fa-solid fa-heart"></i>
            </button>
        </div>
        <div class="card-body">
            <h4>Soto Ayam Lamongan</h4>
            <div class="meta-info">
                <span><i class="fa-solid fa-star"></i> 5.0</span>
                <span><i class="fa-regular fa-clock"></i> 60 mnt</span>
            </div>
            <div class="card-footer">
                <span><i class="fa-regular fa-eye"></i> 445</span>
                <i class="fa-regular fa-bookmark bookmark-icon"></i>
            </div>
        </div>
    </div>
    </div>
</main>