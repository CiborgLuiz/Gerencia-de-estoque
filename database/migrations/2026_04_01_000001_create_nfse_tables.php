<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('service_code', 10);
            $table->string('municipal_tax_code', 60)->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->decimal('iss_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_value', 12, 2);
            $table->decimal('total_tax', 12, 2)->default(0);
            $table->string('status', 30)->default('pendente');
            $table->string('number')->nullable();
            $table->string('rps_number', 20);
            $table->string('protocol')->nullable();
            $table->string('verification_code', 40)->nullable();
            $table->longText('xml')->nullable();
            $table->json('response_payload')->nullable();
            $table->json('customer_data');
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });

        Schema::create('service_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_catalog_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->string('service_code', 10);
            $table->string('municipal_tax_code', 60)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->decimal('iss_rate', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_invoice_items');
        Schema::dropIfExists('service_invoices');
        Schema::dropIfExists('service_catalog_items');
    }
};
