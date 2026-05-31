<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBahanRequest;
use App\Http\Requests\Admin\UpdateBahanRequest;
use App\Models\Bahan;
use App\Services\Admin\AdminBahanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBahanController extends Controller
{
    public function __construct(
        private readonly AdminBahanService $bahanService
    ) {}

    // ──────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $filters = [
            'search'  => $request->string('search')->toString() ?: null,
            'expired' => $request->input('expired'),
        ];

        return view('pages.admin.management_bahan.index', [
            'bahans' => $this->bahanService->getPaginatedBahans($filters),
        ]);
    }

    public function store(StoreBahanRequest $request): RedirectResponse
    {
        $bahan = $this->bahanService->createBahan($request->validated());

        return redirect()
            ->route('admin.bahan.index')
            ->with('success', "Bahan \"{$bahan->nama}\" berhasil ditambahkan.");
    }

    public function update(UpdateBahanRequest $request, Bahan $bahan): RedirectResponse
    {
        $updated = $this->bahanService->updateBahan($bahan, $request->validated());

        return redirect()
            ->route('admin.bahan.index')
            ->with('success', "Bahan \"{$updated->nama}\" berhasil diperbarui.");
    }

    public function destroy(Bahan $bahan): RedirectResponse
    {
        try {
            $nama = $this->bahanService->deleteBahan($bahan);

            return redirect()
                ->route('admin.bahan.index')
                ->with('success', "Bahan \"{$nama}\" berhasil dihapus.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}