<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Resep;
use Illuminate\Http\Request;

class MainMenu extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->query('m');
        $filters = (array) $request->query('filter', []);

        $query = Resep::with(['user.verify']);

        if ($mode === 'favorit') {
            $query->withCount('favoritedBy')->orderBy('favorited_by_count', 'desc');
        } elseif ($mode === 'hari_ini') {
            $query->whereDate('created_at', today());
        } else {
            $query->latest();
        }

        if (!empty($filters)) {
            foreach ($filters as $filterId) {
                $query->whereHas('filters', function ($q) use ($filterId) {
                    $q->where('filters.id', $filterId);
                });
            }
        }

        $reseps = $query->get();
        switch (count($filters)) {
            case 0:
                $master_filters = Filter::where('level', 1)->get();
                break;

            case 1:
                $master_filters = Filter::where('level', 2)->get();
                break;

            case 2:
                $master_filters = Filter::where('level', 3)->get();
                break;

            default:
                $master_filters = [];
                break;
        }
        return view('pages.main-menu.main-menu', compact(
            'reseps',
            'master_filters',
        ));
    }
}
