<?php

namespace App\Services\Admin;

use App\Models\Bahan;
use App\Models\Filter;
use App\Models\Resep;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AdminDashboardService
{
    public function getStats(): array
    {
        return Cache::remember('admin.dashboard.stats', now()->addMinutes(5), fn() => [
            'total_users'    => User::count(),
            'total_resep'    => Resep::count(),
            'total_bahan'    => Bahan::count(),
            'total_filter'   => Filter::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
        ]);
    }

    public function getLatestReseps(): Collection
    {
        return Resep::with(['user', 'mainFilter'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getLatestUsers(): Collection
    {
        return User::withCount('reseps')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getDashboardData(): array
    {
        return [
            ...$this->getStats(),
            'latest_reseps' => $this->getLatestReseps(),
            'latest_users'  => $this->getLatestUsers(),
        ];
    }
}