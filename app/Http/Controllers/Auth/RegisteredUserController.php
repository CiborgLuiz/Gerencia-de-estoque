<?php

namespace App\Http\Controllers\Auth;

use App\Domains\User\Requests\RegisterUserRequest;
use App\Http\Controllers\Controller;
use App\Models\AccessKey;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $accessKey = AccessKey::query()
            ->where('code', $request->string('access_key'))
            ->whereNull('used_at')
            ->when(
                Schema::hasColumn('access_keys', 'revoked_at'),
                fn ($query) => $query->whereNull('revoked_at')
            )
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->with('role')
            ->first();

        if (!$accessKey) {
            throw ValidationException::withMessages([
                'access_key' => 'Chave de acesso inválida, expirada ou já utilizada.',
            ]);
        }

        $iden = strtolower((string) ($accessKey->role?->name ?? User::ROLE_SELLER));

        $user = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
            'role_id' => $accessKey->role_id,
            'iden' => $iden,
        ]);

        $accessKey->update(['used_at' => now()]);

        Auth::login($user);

        $route = $user->isAdmin() ? 'admin.dashboard' : 'dashboard';

        return redirect(route($route, absolute: false));
    }
}
