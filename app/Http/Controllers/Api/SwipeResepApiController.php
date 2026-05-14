<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Filter;
use App\Models\Resep;

class SwipeResepApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET RASA SWIPE
    |--------------------------------------------------------------------------
    */
    public function getRasa()
    {
        $filters = Filter::where(
            'level',
            3
        )->get();

        return response()->json([
            'success' => true,
            'data' => $filters
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER RESEP
    |--------------------------------------------------------------------------
    */
    public function filterResep(Request $request)
    {
        $filterIds = $request->filters ?? [];

        $reseps = Resep::with('user')
            ->whereHas('filters', function ($query) use ($filterIds) {

                $query->whereIn(
                    'filters.id',
                    $filterIds
                );

            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reseps
        ]);
    }
}