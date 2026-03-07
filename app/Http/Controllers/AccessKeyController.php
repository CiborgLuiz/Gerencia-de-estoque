<?php

namespace App\Http\Controllers;

use App\Models\AccessKey;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AccessKeyController extends Controller
{
    public function index(): View
    {
        $keys = AccessKey::query()
            ->with('role')
            ->latest()
            ->paginate(20);

        $roles = Role::query()
            ->whereIn('name', ['gerente', 'vendedor'])
            ->orderBy('name')
            ->get();

        return view('admin.access-keys.index', compact('keys', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $role = Role::query()->findOrFail($validated['role_id']);

        AccessKey::create([
            'code' => strtoupper($role->name).'-'.strtoupper((string) str()->random(10)),
            'role_id' => $role->id,
            'expires_at' => null,
            'used_at' => null,
        ]);

        return back()->with('success', 'Chave criada com sucesso.');
    }

    public function revoke(AccessKey $accessKey): RedirectResponse
    {
        if ($accessKey->used_at) {
            return back()->withErrors([
                'access_key' => 'Não é possível desligar uma chave já utilizada.',
            ]);
        }

        $hasRevokedColumn = Schema::hasColumn('access_keys', 'revoked_at');
        $isRevoked = $hasRevokedColumn
            ? (bool) $accessKey->revoked_at
            : (bool) ($accessKey->expires_at && $accessKey->expires_at->isPast());

        if ($isRevoked) {
            return back()->with('success', 'Chave já está desligada.');
        }

        if ($hasRevokedColumn) {
            $accessKey->update([
                'revoked_at' => now(),
            ]);
        } else {
            // Fallback para bancos sem coluna revoked_at.
            $accessKey->update([
                'expires_at' => now()->subSecond(),
            ]);
        }

        return back()->with('success', 'Chave desligada com sucesso.');
    }
}
