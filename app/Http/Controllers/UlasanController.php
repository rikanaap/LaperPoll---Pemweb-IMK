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
    // ── SHOW halaman ulasan ───────────────────────────────────────────────────
    public function show($id)
    {
        $resep = Resep::with(['feedbacks.user', 'feedbacks.photos'])->findOrFail($id);

        $sudahUlasan  = false;
        $myFeedback   = null;

        if (Auth::check()) {
            $myFeedback  = $resep->feedbacks()->where('user_id', Auth::id())->with('photos')->first();
            $sudahUlasan = $myFeedback !== null;
        }

        $dariTimer = request()->query('from') === 'timer';

        return view('pages.ulasan.ulasan', compact('resep', 'sudahUlasan', 'myFeedback', 'dariTimer'));
    }

    // ── STORE ulasan baru ─────────────────────────────────────────────────────
    public function store(Request $request, $id)
    {
        $resep = Resep::findOrFail($id);

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

        if ($resep->feedbacks()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'Kamu sudah memberikan ulasan untuk resep ini.');
        }

        $feedback = Feedback::create([
            'resep_id'    => $resep->id,
            'user_id'     => Auth::id(),
            'rating'      => $request->rating,
            'description' => $request->description,
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('feedback_photos', 'public');
                FeedbackPhoto::create([
                    'feedback_id' => $feedback->id,
                    'path'        => 'storage/' . $path,
                ]);
            }
        }

        $this->updateAvgRating($resep);

        return redirect()->route('detail.resep', $resep->id)
            ->with('success', 'Ulasan berhasil dikirim! Terima kasih.');
    }

    // ── EDIT form ulasan ──────────────────────────────────────────────────────
    public function edit($resepId, $feedbackId)
    {
        $resep    = Resep::with(['feedbacks.user', 'feedbacks.photos'])->findOrFail($resepId);
        $feedback = Feedback::with('photos')->where('id', $feedbackId)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        $dariTimer   = false;
        $sudahUlasan = true;
        $myFeedback  = $feedback;
        $editMode    = true;

        return view('pages.ulasan.ulasan', compact(
            'resep', 'sudahUlasan', 'myFeedback', 'dariTimer', 'editMode', 'feedback'
        ));
    }

    // ── UPDATE ulasan ─────────────────────────────────────────────────────────
    public function update(Request $request, $resepId, $feedbackId)
    {
        $resep    = Resep::findOrFail($resepId);
        $feedback = Feedback::where('id', $feedbackId)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        $request->validate([
            'rating'           => 'required|numeric|min:1|max:5',
            'description'      => 'nullable|string|max:1000',
            'photos'           => 'nullable|array|max:3',
            'photos.*'         => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_photos'    => 'nullable|array',
            'delete_photos.*'  => 'integer',
        ], [
            'rating.required' => 'Pilih bintang rating terlebih dahulu.',
        ]);

        $feedback->update([
            'rating'      => $request->rating,
            'description' => $request->description,
        ]);

        // Hapus foto yang dipilih user untuk dihapus
        if ($request->filled('delete_photos')) {
            $toDelete = FeedbackPhoto::whereIn('id', $request->delete_photos)
                            ->where('feedback_id', $feedback->id)
                            ->get();
            foreach ($toDelete as $photo) {
                // Hapus file dari storage
                $storagePath = str_replace('storage/', '', $photo->path);
                Storage::disk('public')->delete($storagePath);
                $photo->delete();
            }
        }

        // Tambah foto baru
        if ($request->hasFile('photos')) {
            $existingCount = $feedback->photos()->count();
            $slots         = 3 - $existingCount;
            foreach (array_slice($request->file('photos'), 0, $slots) as $photo) {
                $path = $photo->store('feedback_photos', 'public');
                FeedbackPhoto::create([
                    'feedback_id' => $feedback->id,
                    'path'        => 'storage/' . $path,
                ]);
            }
        }

        $this->updateAvgRating($resep);

        return redirect()->route('detail.resep', $resep->id)
            ->with('success', 'Ulasan berhasil diperbarui!');
    }

    // ── DESTROY ulasan ────────────────────────────────────────────────────────
    public function destroy($resepId, $feedbackId)
    {
        $resep    = Resep::findOrFail($resepId);
        $feedback = Feedback::with('photos')
                        ->where('id', $feedbackId)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        // Hapus semua foto dari storage
        foreach ($feedback->photos as $photo) {
            $storagePath = str_replace('storage/', '', $photo->path);
            Storage::disk('public')->delete($storagePath);
        }

        $feedback->delete();

        $this->updateAvgRating($resep);

        return redirect()->route('detail.resep', $resep->id)
            ->with('success', 'Ulasan berhasil dihapus.');
    }

    // ── HELPER update rata-rata rating ───────────────────────────────────────
    private function updateAvgRating(Resep $resep)
    {
        $avg = $resep->feedbacks()->avg('rating') ?? 0;
        $resep->update(['current_star' => round($avg, 1)]);
    }
}