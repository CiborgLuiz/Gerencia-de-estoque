<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            try {
                $table->dropUnique('categories_name_unique');
            } catch (Throwable) {
                // Index might not exist in some environments.
            }

            try {
                $table->unique(['parent_id', 'name'], 'categories_parent_name_unique');
            } catch (Throwable) {
                // Index may already exist.
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            try {
                $table->dropUnique('categories_parent_name_unique');
            } catch (Throwable) {
                // Ignore if missing.
            }

            try {
                $table->unique('name', 'categories_name_unique');
            } catch (Throwable) {
                // Ignore if exists.
            }
        });
    }
};
