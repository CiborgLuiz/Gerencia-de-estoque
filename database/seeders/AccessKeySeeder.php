<?php

namespace Database\Seeders;

use App\Models\AccessKey;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AccessKeySeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'gerente' => 'GERENTE-ACESSO-001',
            'vendedor' => 'VENDEDOR-ACESSO-001',
        ];

        foreach ($roles as $roleName => $code) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                continue;
            }

            AccessKey::updateOrCreate(
                ['code' => $code],
                [
                    'role_id' => $role->id,
                    'expires_at' => null,
                    'used_at' => null,
                ]
            );
        }
    }
}
