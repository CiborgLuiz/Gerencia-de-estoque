<?php

use App\Models\ServiceCatalogItem;
use App\Models\User;

test('nfse screen can be rendered for verified users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('nfse.index'));

    $response->assertOk();
});

test('service catalog item can be created for nfse', function () {
    $user = User::factory()->create(['iden' => 'admin']);

    $response = $this->actingAs($user)->post(route('nfse.catalog-items.store'), [
        'description' => 'Troca de oleo',
        'service_code' => '14.01',
        'municipal_tax_code' => '1401',
        'unit_price' => 180.50,
        'iss_rate' => 2.00,
        'is_active' => 1,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('service_catalog_items', [
        'description' => 'Troca de oleo',
        'service_code' => '14.01',
    ]);
});

test('nfse can be issued in mock mode', function () {
    $user = User::factory()->create();
    $catalogItem = ServiceCatalogItem::query()->create([
        'description' => 'Alinhamento completo',
        'service_code' => '14.01',
        'municipal_tax_code' => '1401',
        'unit_price' => 250.00,
        'iss_rate' => 2.00,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->post(route('nfse.store'), [
        'service_catalog_item_id' => $catalogItem->id,
        'quantity' => 1,
        'customer_name' => 'Cliente Teste',
        'customer_document' => '12345678901',
        'customer_email' => 'cliente@example.com',
        'customer_phone' => '11999999999',
        'customer_address' => 'Rua A',
        'customer_number' => '100',
        'customer_neighborhood' => 'Centro',
        'customer_city_code' => '3550308',
        'customer_state' => 'SP',
        'customer_zip_code' => '01001000',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('service_invoices', [
        'status' => 'emitida',
    ]);
    $this->assertDatabaseHas('service_invoice_items', [
        'description' => 'Alinhamento completo',
        'service_code' => '14.01',
    ]);
});
