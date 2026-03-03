<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('access_keys', function (Blueprint $table) {
            if (Schema::hasColumn('access_keys', 'key') && !Schema::hasColumn('access_keys', 'code')) {
                $table->renameColumn('key', 'code');
            }
        });

        Schema::table('access_keys', function (Blueprint $table) {
            if (Schema::hasColumn('access_keys', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            if (!Schema::hasColumn('access_keys', 'role_id')) {
                $table->foreignId('role_id')->nullable()->after('code')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('access_keys', 'used_at')) {
                $table->timestamp('used_at')->nullable()->after('expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('access_keys', function (Blueprint $table) {
            if (Schema::hasColumn('access_keys', 'role_id')) {
                $table->dropConstrainedForeignId('role_id');
            }
            if (Schema::hasColumn('access_keys', 'used_at')) {
                $table->dropColumn('used_at');
            }
            if (!Schema::hasColumn('access_keys', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
        });

        Schema::table('access_keys', function (Blueprint $table) {
            if (Schema::hasColumn('access_keys', 'code') && !Schema::hasColumn('access_keys', 'key')) {
                $table->renameColumn('code', 'key');
            }
        });
    }
};
