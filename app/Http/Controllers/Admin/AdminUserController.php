<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Models\User;
use App\Services\Admin\AdminUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly AdminUserService $userService
    ) {}


    public function index(UserFilterRequest $request): View
    {
        $users = $this->userService->getPaginatedUsers($request->filters());

        return view('pages.admin.management_user.index', [
            'users'     => $users,
            'verifData' => $this->userService->buildVerifData($users),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->userService->createUser($request->validated());

        return redirect()
            ->route('admin.user.index')
            ->with('success', "User \"{$user->name}\" berhasil ditambahkan.");
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $updated = $this->userService->updateUser($user, $request->validated());

        return back()->with('success', "User \"{$updated->name}\" berhasil diperbarui.");
    }

    public function verify(User $user): RedirectResponse
    {
        try {
            $verified = $this->userService->verifyUser($user);

            return back()->with('success', "User \"{$verified->name}\" berhasil diverifikasi.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $name = $this->userService->deleteUser($user);

            return redirect()
                ->route('admin.user.index')
                ->with('success', "User \"{$name}\" berhasil dihapus.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}