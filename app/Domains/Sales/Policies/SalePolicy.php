<?php

namespace App\Domains\Sales\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_SELLER);
    }

    public function viewAny(User $user): bool
    {
        return $this->create($user);
    }

    public function view(User $user, Sale $sale): bool
    {
        return $user->isAdmin() || $sale->user_id === $user->id;
    }
}
