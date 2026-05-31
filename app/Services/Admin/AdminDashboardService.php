<?php

namespace App\Services\Admin;

use App\Models\Feedback;
use App\Models\Resep;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminDashboardService
{
 
    public function getStats(): array
    {
        return [
            'total_users'    => User::count(),
            'total_resep'    => Resep::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'total_feedback' => Feedback::count(),
        ];
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