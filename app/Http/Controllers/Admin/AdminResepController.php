<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResepFilterRequest;
use App\Models\Resep;
use App\Services\Admin\AdminResepService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminResepController extends Controller
{
    public function __construct(
        private readonly AdminResepService $resepService
    ) {}

    public function index(ResepFilterRequest $request): View
    {
        return view('pages.admin.management_resep.index', [
            'reseps'   => $this->resepService->getPaginatedReseps($request->filters()),
            'kategoris' => $this->resepService->getAllFilters(), // ✅ key match dengan view
        ]);

        
    }

    public function show(Resep $resep): View
    {
        return view('pages.admin.management_resep.show', [
            'resep' => $this->resepService->getResepDetail($resep),
        ]);
    }

    public function togglePublish(Resep $resep): RedirectResponse
    {
        $isPublished = $this->resepService->togglePublish($resep);

        return back()->with('success', $isPublished
            ? "Resep \"{$resep->title}\" berhasil dipublish."
            : "Resep \"{$resep->title}\" berhasil di-unpublish."
        );
    }

    public function destroy(Resep $resep): RedirectResponse
    {
        $title = $this->resepService->deleteResep($resep);

        return redirect()
            ->route('admin.resep.index')
            ->with('success', "Resep \"{$title}\" berhasil dihapus.");
    }
}