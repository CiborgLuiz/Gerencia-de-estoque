<?php

namespace App\Domains\Sales\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_OWNER, User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_SELLER);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_OWNER, User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_SELLER);
    }

    public function update(User $user, Sale $sale): bool
    {
        return $user->isAdmin() || $sale->user_id === $user->id;
    }
}
