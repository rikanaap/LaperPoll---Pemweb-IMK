<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterQueryRequest;
use App\Http\Requests\Admin\StoreFilterRequest;
use App\Http\Requests\Admin\UpdateFilterRequest;
use App\Models\Filter;
use App\Services\Admin\AdminFilterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminFilterController extends Controller
{
    public function __construct(
        private readonly AdminFilterService $filterService
    ) {}

    public function index(FilterQueryRequest $request): View
    {
        return view('pages.admin.management_filter.index', [
            'filterList'      => $this->filterService->getPaginatedFilters($request->filters()),
            'availableLevels' => $this->filterService->getAvailableLevels(),
        ]);
    }

    public function store(StoreFilterRequest $request): RedirectResponse
    {
        $filter = $this->filterService->createFilter($request->validated());

        return redirect()
            ->route('admin.filter.index')
            ->with('success', "Filter \"{$filter->title}\" berhasil ditambahkan.");
    }

    public function update(UpdateFilterRequest $request, Filter $filter): RedirectResponse
    {
        $updated = $this->filterService->updateFilter($filter, $request->validated());

        return redirect()
            ->route('admin.filter.index')
            ->with('success', "Filter \"{$updated->title}\" berhasil diperbarui.");
    }

    public function destroy(Filter $filter): RedirectResponse
    {
        try {
            $title = $this->filterService->deleteFilter($filter);

            return redirect()
                ->route('admin.filter.index')
                ->with('success', "Filter \"{$title}\" berhasil dihapus.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}