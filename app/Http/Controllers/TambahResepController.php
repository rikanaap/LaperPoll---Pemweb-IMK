<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Filter;
use App\Models\LangkahBahan;
use App\Models\LangkahResep;
use App\Models\Resep;
use App\Models\ResepAttachment;
use App\Models\ResepBahan;
use App\Models\ResepFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class TambahResepController extends Controller
{
    public function index()
    {
        $bahans = Bahan::all();
        $filters = Filter::where('level', 2)
            ->orWhere('level', 3)
            ->get();
        $kategories = Filter::where('level', 1)->get();

        return view('pages.resep.tambah', [
            'bahans' => $bahans,
            'filters' => $filters,
            'kategories' => $kategories,
        ]);
    }

    /**
     * Get bahan data for dropdown (AJAX)
     */
    public function getBahans()
    {
        $bahans = Bahan::select('id', 'nama')->get();
        return response()->json($bahans);
    }

    /**
     * Get filters data for dropdown (AJAX)
     */
    public function getFilters()
    {
        $filters = Filter::select('id', 'title', 'level')->get();
        return response()->json($filters);
    }

    /**
     * Submit resep baru
     */
    public function store(Request $request)
    {
        try {
            $resep = Resep::create([
                'user_id'        => Auth::id(),
                'title'          => $request->title,
                'calorie'        => $request->calorie,
                'cook_duration' => $request->cook_duration,
                'main_filter_id' => $request->main_filter_id,
                'is_published'   => true,
            ]);

            // Bahans
            foreach ($request->bahans ?? [] as $b) {
                ResepBahan::create([
                    'resep_id'   => $resep->id,
                    'bahan_id'   => $b['bahan_id'],
                    'gram_total' => $b['gram_total'],
                ]);
            }

            // Filters
            foreach ($request->filters ?? [] as $f) {
                ResepFilter::create([
                    'resep_id'   => $resep->id,
                    'filters_id' => $f['filters_id'],
                ]);
            }

            // Steps + bahan per step
            foreach ($request->steps ?? [] as $s) {
                $langkah = LangkahResep::create([
                    'resep_id'      => $resep->id,
                    'step_order'    => $s['step_order'],
                    'step_duration' => $s['step_duration'],
                    'description'   => $s['description'],
                ]);

                foreach ($s['bahans'] ?? [] as $b) {
                    // Cari resep_bahan_id yang cocok
                    $resepBahan = ResepBahan::where('resep_id', $resep->id)
                        ->where('bahan_id', $b['bahan_id'])
                        ->first();

                    if ($resepBahan) {
                        LangkahBahan::create([
                            'langkah_id'    => $langkah->id,
                            'resep_bahan_id' => $resepBahan->id,
                            'gram_total'    => $b['gram_total'],
                        ]);
                    }
                }
            }

            // Attachments
            foreach ($request->file('attachments') ?? [] as $file) {
                $mimetype = $file->getMimeType(); // simpan dulu sebelum move
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/reseps'), $filename);
                $path = 'assets/images/reseps/' . $filename;
                ResepAttachment::create([
                    'resep_id' => $resep->id,
                    'mimetype' => $mimetype,
                    'path'     => $path,
                ]);
            }
            return response()->json([
                'redirect' => route('profile.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ], 204);
        }
    }

    /**
     * Clear all form sessions/cookies
     */
    public function clearForm()
    {
        return redirect()
            ->withCookie(Cookie::forget('form_bahans'))
            ->withCookie(Cookie::forget('form_filters'))
            ->withCookie(Cookie::forget('form_steps'))
            ->withCookie(Cookie::forget('form_attachments'))
            ->back();
    }

    /**
     * Tampilkan form edit resep
     */
    public function edit($id)
    {
        $resep = Resep::findOrFail($id);  // ← tanpa with() dulu

    if ($resep->user_id !== Auth::id()) {
        abort(403);
    }

    $bahans     = Bahan::all();
    $filters    = Filter::where('level', 2)->orWhere('level', 3)->get();
    $kategories = Filter::where('level', 1)->get();

    return view('pages.resep.edit', compact('resep', 'bahans', 'filters', 'kategories'));
    }

    /**
     * Simpan perubahan resep
     */
    public function update(Request $request, $id)
    {
        $resep = Resep::findOrFail($id);

        if ($resep->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak punya akses untuk mengedit resep ini.');
        }

        try {
            // ── Update kolom utama resep ──
            $resep->update([
                'title'          => $request->title,
                'calorie'        => $request->calorie,
                'cook_duration'  => $request->cook_duration,
                'main_filter_id' => $request->main_filter_id,
            ]);

            // ── Update Bahans: hapus lama, insert baru ──
            // Hapus LangkahBahan dulu (foreign key ke ResepBahan)
            foreach ($resep->resepBahans as $rb) {
                $rb->langkahBahans()->delete();
            }
            $resep->resepBahans()->delete();

            $resepBahanMap = []; // [bahan_id => resep_bahan_id] untuk lookup langkah
            foreach ($request->bahans ?? [] as $b) {
                $rb = ResepBahan::create([
                    'resep_id'   => $resep->id,
                    'bahan_id'   => $b['bahan_id'],
                    'gram_total' => $b['gram_total'],
                ]);
                $resepBahanMap[$b['bahan_id']] = $rb->id;
            }

            // ── Update Filters ──
            $resep->filters()->delete(); // atau sync jika many-to-many
            foreach ($request->filters ?? [] as $f) {
                ResepFilter::create([
                    'resep_id'   => $resep->id,
                    'filters_id' => $f['filters_id'],
                ]);
            }

            // ── Update Langkah + LangkahBahan ──
            foreach ($resep->langkahReseps as $langkah) {
                $langkah->langkahBahans()->delete();
            }
            $resep->langkahReseps()->delete();

            foreach ($request->steps ?? [] as $s) {
                $langkah = LangkahResep::create([
                    'resep_id'      => $resep->id,
                    'step_order'    => $s['step_order'],
                    'step_duration' => $s['step_duration'],
                    'description'   => $s['description'],
                ]);

                foreach ($s['bahans'] ?? [] as $b) {
                    $resepBahanId = $resepBahanMap[$b['bahan_id']] ?? null;
                    if ($resepBahanId) {
                        LangkahBahan::create([
                            'langkah_id'     => $langkah->id,
                            'resep_bahan_id' => $resepBahanId,
                            'gram_total'     => $b['gram_total'],
                        ]);
                    }
                }
            }

            // ── Update Attachments (hanya kalau ada file baru) ──
            if ($request->hasFile('attachments')) {
                // Hapus attachment lama
                $resep->attachments()->delete();

                foreach ($request->file('attachments') as $file) {
                    $mimetype = $file->getMimeType();
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('assets/images/reseps'), $filename);
                    ResepAttachment::create([
                        'resep_id' => $resep->id,
                        'mimetype' => $mimetype,
                        'path'     => 'assets/images/reseps/' . $filename,
                    ]);
                }
            }

            return response()->json([
                'redirect' => route('profile.index'),
                'message'  => 'Resep berhasil diperbarui!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ], 422);
        }
    }
}
