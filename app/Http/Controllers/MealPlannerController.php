<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MealPlannerController extends Controller
{
    public function index()
    {
        return view('pages.meal_planner.index');
    }
}