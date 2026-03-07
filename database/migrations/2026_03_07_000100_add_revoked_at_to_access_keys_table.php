<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('access_keys', function (Blueprint $table) {
            if (!Schema::hasColumn('access_keys', 'revoked_at')) {
                $table->timestamp('revoked_at')->nullable()->after('used_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('access_keys', function (Blueprint $table) {
            if (Schema::hasColumn('access_keys', 'revoked_at')) {
                $table->dropColumn('revoked_at');
            }
        });
    }
};
