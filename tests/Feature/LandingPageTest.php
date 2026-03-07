<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('landing page can be rendered before login', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Bem-vindo ao painel de gerência');
    $response->assertSee('Entrar');
});

test('owner account can authenticate with default seeded credentials', function () {
    $this->seed(DatabaseSeeder::class);

    $response = $this->post('/login', [
        'email' => 'admin@email.com',
        'password' => '123456',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('manager account can authenticate with seeded credentials', function () {
    $this->seed(DatabaseSeeder::class);

    $response = $this->post('/login', [
        'email' => 'gerente@email.com',
        'password' => '123456',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
