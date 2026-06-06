<?php

namespace App\Http\Controllers;

use App\Models\MealPlanner;
use App\Models\MealPlannerDetail;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PilihResepController extends Controller
{
    public function index(Request $request)
    {
        // FIX: parameter yang dikirim meal-planner.js adalah 'tanggal' + 'meal_time'
        $tanggal   = $request->query('tanggal', '');
        $meal_time = $request->query('meal_time', '');

        $reseps = Resep::where('is_published', 1)
            ->select('id', 'title', 'calorie', 'cook_duration', 'thumbnail', 'main_filter_id')
            ->orderBy('title')
            ->get();

        // FIX: cari resep yang sedang aktif di slot ini untuk badge "Terpilih"
        $resepAktifId = null;
        if ($tanggal && $meal_time) {
            $planner = MealPlanner::where('user_id', Auth::id())
                ->where('tanggal', $tanggal)
                ->first();

            if ($planner) {
                $detail = MealPlannerDetail::where('meal_planner_id', $planner->id)
                    ->where('meal_time', $meal_time)
                    ->first();
                $resepAktifId = $detail?->resep_id;
            }
        }

        return view('pages.pilih-resep.index', compact(
            'reseps', 'tanggal', 'meal_time', 'resepAktifId'
        ));
    }
}