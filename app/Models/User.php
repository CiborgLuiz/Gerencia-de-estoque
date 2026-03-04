<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public const ROLE_OWNER = 'dono';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'gerente';
    public const ROLE_SELLER = 'vendedor';

    protected $fillable = [
        'name',
        'email',
        'password',
        'iden',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (!$user->iden && $user->role_id) {
                $roleName = Role::query()->whereKey($user->role_id)->value('name');
                if ($roleName) {
                    $user->iden = strtolower((string) $roleName);
                }
            }

            if (!$user->iden) {
                $user->iden = self::ROLE_SELLER;
            }
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->iden, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_OWNER, self::ROLE_ADMIN);
    }
}
