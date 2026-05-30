<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly AdminDashboardService $dashboardService
    ) {}

    public function index(): View
    {
        return view(
            'pages.admin.dashboard.index',
            $this->dashboardService->getDashboardData()
        );
    }
}