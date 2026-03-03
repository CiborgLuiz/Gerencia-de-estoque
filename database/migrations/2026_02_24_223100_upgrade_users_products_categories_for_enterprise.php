<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'iden')) {
                $table->string('iden', 30)->default('vendedor')->after('password');
            }

            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            }

            if (!Schema::hasColumn('categories', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'internal_code')) {
                $table->string('internal_code')->unique()->after('name');
            }
            if (!Schema::hasColumn('products', 'manufacturer_code')) {
                $table->string('manufacturer_code')->nullable()->after('internal_code');
            }
            if (!Schema::hasColumn('products', 'ncm')) {
                $table->string('ncm', 10)->after('manufacturer_code');
            }
            if (!Schema::hasColumn('products', 'image_path')) {
                $table->string('image_path')->nullable()->after('ncm');
            }
            if (!Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 12, 2)->default(0)->after('description');
            }
            if (!Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 12, 2)->default(0)->after('purchase_price');
            }
            if (!Schema::hasColumn('products', 'minimum_stock')) {
                $table->unsignedInteger('minimum_stock')->default(0)->after('stock');
            }
            if (!Schema::hasColumn('products', 'status')) {
                $table->string('status', 20)->default('ativo')->after('minimum_stock');
            }
            if (!Schema::hasColumn('products', 'cfop')) {
                $table->string('cfop', 4)->nullable()->after('status');
            }
            if (!Schema::hasColumn('products', 'cst_csosn')) {
                $table->string('cst_csosn', 4)->nullable()->after('cfop');
            }
            if (!Schema::hasColumn('products', 'origin')) {
                $table->string('origin', 2)->default('0')->after('cst_csosn');
            }
            if (!Schema::hasColumn('products', 'icms_rate')) {
                $table->decimal('icms_rate', 5, 2)->default(0)->after('origin');
            }
            if (!Schema::hasColumn('products', 'pis_rate')) {
                $table->decimal('pis_rate', 5, 2)->default(0)->after('icms_rate');
            }
            if (!Schema::hasColumn('products', 'cofins_rate')) {
                $table->decimal('cofins_rate', 5, 2)->default(0)->after('pis_rate');
            }
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'internal_code',
                'manufacturer_code',
                'ncm',
                'image_path',
                'purchase_price',
                'sale_price',
                'minimum_stock',
                'status',
                'cfop',
                'cst_csosn',
                'origin',
                'icms_rate',
                'pis_rate',
                'cofins_rate',
            ]);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('iden');
            $table->dropSoftDeletes();
        });
    }
};
