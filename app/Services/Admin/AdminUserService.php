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

    // Persyaratan verifikasi
    private const VERIF_MIN_RESEP     = 50;
    private const VERIF_MIN_FAVORIT   = 300;
    private const VERIF_MIN_FOLLOWERS = 80;
    private const VERIF_MIN_VIEWS     = 1000;

    // ──────────────────────────────────────────────────────────
    // Read
    // ──────────────────────────────────────────────────────────

    public function getPaginatedUsers(array $filters): LengthAwarePaginator
    {
        return User::query()
            // Jumlah resep milik user
            ->withCount('reseps')
            // Jumlah followers
            ->withCount('followers')
            // Total favorit: jumlah baris di tabel favorites yang resep-nya milik user ini
            ->withCount(['reseps as favorites_count' => function ($query) {
                $query->whereHas('favoritedBy');
            }])
            // Total views: sum views_count dari semua resep milik user
            ->addSelect([
                'total_views' => DB::table('reseps')
                    ->selectRaw('COALESCE(SUM(views_count), 0)')
                    ->whereColumn('reseps.user_id', 'users.id'),
            ])
            ->when($filters['search'], function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
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
        return $users->getCollection()->mapWithKeys(function (User $user) {
            $totalResep     = (int) ($user->reseps_count    ?? 0);
            $totalFavorit   = (int) ($user->favorites_count ?? 0);
            $totalFollowers = (int) ($user->followers_count ?? 0);
            $totalViews     = (int) ($user->total_views     ?? 0);

            return [
                $user->id => [
                    'resep_count'     => $totalResep,
                    'favorit_count'   => $totalFavorit,
                    'followers_count' => $totalFollowers,
                    'views_count'     => $totalViews,

                    'pass_resep'     => $totalResep     >= self::VERIF_MIN_RESEP,
                    'pass_favorit'   => $totalFavorit   >= self::VERIF_MIN_FAVORIT,
                    'pass_followers' => $totalFollowers >= self::VERIF_MIN_FOLLOWERS,
                    'pass_views'     => $totalViews     >= self::VERIF_MIN_VIEWS,

                    'min_resep'     => self::VERIF_MIN_RESEP,
                    'min_favorit'   => self::VERIF_MIN_FAVORIT,
                    'min_followers' => self::VERIF_MIN_FOLLOWERS,
                    'min_views'     => self::VERIF_MIN_VIEWS,
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

        $totalViews = DB::table('reseps')
            ->where('user_id', $user->id)
            ->sum('views_count');

        return $user->reseps_count    >= self::VERIF_MIN_RESEP
            && $favorites             >= self::VERIF_MIN_FAVORIT
            && $user->followers_count >= self::VERIF_MIN_FOLLOWERS
            && $totalViews            >= self::VERIF_MIN_VIEWS;
    }


    public function createUser(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => (bool) ($data['is_admin'] ?? false),
        ]);
    }

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
}