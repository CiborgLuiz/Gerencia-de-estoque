<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $payload = [
            'password' => Hash::make('donodaempresa123-senha'),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('users', 'email_verified_at')) {
            $payload['email_verified_at'] = now();
        }

        DB::table('users')
            ->where('email', 'admin@email.com')
            ->update($payload);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'admin@email.com')
            ->update([
                'updated_at' => now(),
            ]);
    }
};
