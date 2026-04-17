<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    public function up(): void
    {
        foreach (['dono', 'admin', 'gerente', 'vendedor'] as $roleName) {
            DB::table('roles')->updateOrInsert(
                ['name' => $roleName],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }

        $ownerRoleId = DB::table('roles')->where('name', 'dono')->value('id');
        $managerRoleId = DB::table('roles')->where('name', 'gerente')->value('id');

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@email.com'],
            [
                'name' => 'Dono da Empresa',
                'password' => Hash::make('donodaempresa123-senha'),
                'email_verified_at' => now(),
                'iden' => 'dono',
                'role_id' => $ownerRoleId,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'gerente@email.com'],
            [
                'name' => 'Gerente Base',
                'password' => Hash::make('123456'),
                'iden' => 'gerente',
                'role_id' => $managerRoleId,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('access_keys')->updateOrInsert(
            ['code' => 'GERENTE-ACESSO-001'],
            [
                'role_id' => $managerRoleId,
                'expires_at' => null,
                'used_at' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $sellerRoleId = DB::table('roles')->where('name', 'vendedor')->value('id');

        DB::table('access_keys')->updateOrInsert(
            ['code' => 'VENDEDOR-ACESSO-001'],
            [
                'role_id' => $sellerRoleId,
                'expires_at' => null,
                'used_at' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('access_keys')->whereIn('code', [
            'GERENTE-ACESSO-001',
            'VENDEDOR-ACESSO-001',
        ])->delete();

        DB::table('users')->whereIn('email', [
            'admin@email.com',
            'gerente@email.com',
        ])->delete();
    }
};
