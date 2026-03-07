<?php

use App\Models\AccessKey;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates owner and manager accounts with access keys', function () {
    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseHas('users', [
        'email' => 'admin@email.com',
        'iden' => 'dono',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'gerente@email.com',
        'iden' => 'gerente',
    ]);

    $owner = User::where('email', 'admin@email.com')->first();
    expect($owner)->not->toBeNull();

    $codes = AccessKey::pluck('code')->all();
    expect($codes)->toContain('GERENTE-ACESSO-001', 'VENDEDOR-ACESSO-001');
});
