<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;

class TimerResepController extends Controller
{
    public function show($id)
    {
        $resep = Resep::with(['langkahs', 'bahans', 'user'])->findOrFail($id);

        $langkahs = $resep->langkahs->sortBy('step_order')->values();

        // Siapkan data plain array — hindari closure di dalam @json blade
        $stepsData = $langkahs->map(function ($l, $i) {
            return [
                'index'         => $i,
                'label'         => 'Langkah ' . $l->step_order,
                'description'   => $l->description,
                'step_duration' => $l->step_duration,
            ];
        })->values()->toArray();

        $bahansData = $resep->bahans->map(function ($b) {
            return [
                'nama' => $b->nama,
                'gram' => $b->pivot->gram_total,
            ];
        })->values()->toArray();

        return view('pages.timer_resep.timer_resep', compact(
            'resep', 'langkahs', 'stepsData', 'bahansData'
        ));
    }
}