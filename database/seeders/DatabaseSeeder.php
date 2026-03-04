<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AccessKeySeeder::class,
        ]);

        $ownerRole = Role::where('name', 'dono')->first();

        User::updateOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name' => 'Dono da Empresa',
                'password' => bcrypt('123456'),
                'role_id' => $ownerRole?->id,
                'iden' => 'dono',
            ]
        );
    }
}
