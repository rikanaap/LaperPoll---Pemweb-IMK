<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserService
{
    private const PER_PAGE = 15;

    private const VERIF_REQUIREMENTS = [
        'resep'     => 50,
        'favorit'   => 300,
        'followers' => 80,
        'views'     => 1000,
    ];

    // ──────────────────────────────────────────────────────────
    // Read
    // ──────────────────────────────────────────────────────────

    public function getPaginatedUsers(array $filters): LengthAwarePaginator
    {
        return User::query()
            ->withCount('reseps')
            ->withCount('followers')
            ->withCount(['reseps as favorites_count' => fn ($q) =>
                $q->whereHas('favoritedBy')
            ])
            ->addSelect([
                'total_views' => DB::table('reseps')
                    ->selectRaw('COALESCE(SUM(views_count), 0)')
                    ->whereColumn('reseps.user_id', 'users.id'),
            ])
            ->when($filters['search'], fn ($q, $search) =>
                $q->where(fn ($q) =>
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                )
            )
            ->when($filters['verif'] === 'verified',   fn ($q) => $q->whereNotNull('email_verified_at'))
            ->when($filters['verif'] === 'unverified', fn ($q) => $q->whereNull('email_verified_at'))
            ->when($filters['role'] === 'admin', fn ($q) => $q->where('is_admin', true))
            ->when($filters['role'] === 'user',  fn ($q) => $q->where('is_admin', false))
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    public function buildVerifData(LengthAwarePaginator $users): Collection
    {
        $req = self::VERIF_REQUIREMENTS;

        return $users->getCollection()->mapWithKeys(function (User $user) use ($req) {
            $stats = $this->extractUserStats($user);

            return [
                $user->id => [
                    'resep_count'     => $stats['resep'],
                    'favorit_count'   => $stats['favorit'],
                    'followers_count' => $stats['followers'],
                    'views_count'     => $stats['views'],

                    'pass_resep'     => $stats['resep']     >= $req['resep'],
                    'pass_favorit'   => $stats['favorit']   >= $req['favorit'],
                    'pass_followers' => $stats['followers'] >= $req['followers'],
                    'pass_views'     => $stats['views']     >= $req['views'],

                    'min_resep'     => $req['resep'],
                    'min_favorit'   => $req['favorit'],
                    'min_followers' => $req['followers'],
                    'min_views'     => $req['views'],
                ],
            ];
        });
    }

    public function meetsVerifRequirements(User $user): bool
    {
        $user->loadCount(['reseps', 'followers']);

        $favorites = DB::table('favorites')
            ->join('reseps', 'reseps.id', '=', 'favorites.resep_id')
            ->where('reseps.user_id', $user->id)
            ->count();

        $totalViews = (int) DB::table('reseps')
            ->where('user_id', $user->id)
            ->sum('views_count');

        $req = self::VERIF_REQUIREMENTS;

        return $user->reseps_count    >= $req['resep']
            && $favorites             >= $req['favorit']
            && $user->followers_count >= $req['followers']
            && $totalViews            >= $req['views'];
    }

    // ──────────────────────────────────────────────────────────
    // Write
    // ──────────────────────────────────────────────────────────

    public function updateUser(User $user, array $data): User
    {
        $payload = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'is_admin' => (bool) ($data['is_admin'] ?? false),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return $user->fresh();
    }

    public function verifyUser(User $user): User
    {
        if ($user->email_verified_at !== null) {
            return $user;
        }

        if (! $this->meetsVerifRequirements($user)) {
            throw new \RuntimeException("User \"{$user->name}\" belum memenuhi syarat verifikasi.");
        }

        $user->update(['email_verified_at' => now()]);

        return $user->fresh();
    }

    public function deleteUser(User $user): string
    {
        if ($user->id === auth()->id()) {
            throw new \RuntimeException('Tidak dapat menghapus akun yang sedang digunakan.');
        }

        $name = $user->name;
        $user->delete();

        return $name;
    }

    // ──────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────

    private function extractUserStats(User $user): array
    {
        return [
            'resep'     => (int) ($user->reseps_count    ?? 0),
            'favorit'   => (int) ($user->favorites_count ?? 0),
            'followers' => (int) ($user->followers_count ?? 0),
            'views'     => (int) ($user->total_views     ?? 0),
        ];
    }
}