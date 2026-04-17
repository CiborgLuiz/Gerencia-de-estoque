<?php

use App\Models\AccessKey;
use App\Models\Role;
use Database\Seeders\RoleSeeder;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register with access key', function () {
    $this->seed(RoleSeeder::class);

    $roleId = Role::query()->where('name', 'vendedor')->value('id');

    AccessKey::query()->create([
        'code' => 'VENDEDOR-TESTE-001',
        'role_id' => $roleId,
        'expires_at' => null,
        'used_at' => null,
    ]);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'access_key' => 'VENDEDOR-TESTE-001',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
