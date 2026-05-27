<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Feedback;
use App\Models\FeedbackPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UlasanController extends Controller
{
    /**
     * Halaman ulasan standalone (opsional, bisa dipakai terpisah dari detail resep)
     */
    public function show($id)
    {
        $resep = Resep::with(['feedbacks.user', 'feedbacks.photos'])->findOrFail($id);

        $sudahUlasan = Auth::check()
            ? $resep->feedbacks()->where('user_id', Auth::id())->exists()
            : false;

        // Deteksi apakah datang dari timer resep
        $dariTimer = request()->query('from') === 'timer';

        return view('pages.ulasan.ulasan', compact('resep', 'sudahUlasan', 'dariTimer'));
    }

    /**
     * Simpan ulasan baru — dipanggil dari form di detail resep
     */
    public function store(Request $request, $id)
    {
        $resep = Resep::findOrFail($id);

        // Validasi
        $request->validate([
            'rating'      => 'required|numeric|min:1|max:5',
            'description' => 'nullable|string|max:1000',
            'photos'      => 'nullable|array|max:3',
            'photos.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'rating.required' => 'Pilih bintang rating terlebih dahulu.',
            'rating.min'      => 'Rating minimal 1 bintang.',
            'photos.*.image'  => 'File harus berupa gambar.',
            'photos.*.max'    => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Cek sudah pernah ulasan
        $sudah = $resep->feedbacks()->where('user_id', Auth::id())->exists();
        if ($sudah) {
            return back()->with('error', 'Kamu sudah memberikan ulasan untuk resep ini.');
        }

        // Simpan feedback
        $feedback = Feedback::create([
            'resep_id'    => $resep->id,
            'user_id'     => Auth::id(),
            'rating'      => $request->rating,
            'description' => $request->description,
        ]);

        // Simpan foto jika ada
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('feedback_photos', 'public');
                FeedbackPhoto::create([
                    'feedback_id' => $feedback->id,
                    'path'        => 'storage/' . $path,
                ]);
            }
        }

        // Update rata-rata rating di tabel resep
        $avgRating = $resep->feedbacks()->avg('rating');
        $resep->update(['current_star' => round($avgRating, 1)]);

        return redirect()
            ->route('detail.resep', $resep->id)
            ->with('success', 'Ulasan berhasil dikirim! Terima kasih.');
    }
}