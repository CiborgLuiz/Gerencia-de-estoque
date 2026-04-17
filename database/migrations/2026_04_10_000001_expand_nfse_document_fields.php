<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('service_catalog_items')) {
            Schema::table('service_catalog_items', function (Blueprint $table) {
                if (!Schema::hasColumn('service_catalog_items', 'long_description')) {
                    $table->text('long_description')->nullable();
                }

                if (!Schema::hasColumn('service_catalog_items', 'national_tax_code')) {
                    $table->string('national_tax_code', 30)->nullable();
                }

                if (!Schema::hasColumn('service_catalog_items', 'nbs_code')) {
                    $table->string('nbs_code', 30)->nullable();
                }
            });
        }

        if (Schema::hasTable('service_invoices') && !Schema::hasColumn('service_invoices', 'service_context')) {
            Schema::table('service_invoices', function (Blueprint $table) {
                $table->json('service_context')->nullable();
            });
        }

        if (Schema::hasTable('service_invoice_items')) {
            Schema::table('service_invoice_items', function (Blueprint $table) {
                if (!Schema::hasColumn('service_invoice_items', 'long_description')) {
                    $table->text('long_description')->nullable();
                }

                if (!Schema::hasColumn('service_invoice_items', 'national_tax_code')) {
                    $table->string('national_tax_code', 30)->nullable();
                }

                if (!Schema::hasColumn('service_invoice_items', 'nbs_code')) {
                    $table->string('nbs_code', 30)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('service_invoice_items')) {
            Schema::table('service_invoice_items', function (Blueprint $table) {
                $columns = [];

                if (Schema::hasColumn('service_invoice_items', 'long_description')) {
                    $columns[] = 'long_description';
                }

                if (Schema::hasColumn('service_invoice_items', 'national_tax_code')) {
                    $columns[] = 'national_tax_code';
                }

                if (Schema::hasColumn('service_invoice_items', 'nbs_code')) {
                    $columns[] = 'nbs_code';
                }

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('service_invoices') && Schema::hasColumn('service_invoices', 'service_context')) {
            Schema::table('service_invoices', function (Blueprint $table) {
                $table->dropColumn('service_context');
            });
        }

        if (Schema::hasTable('service_catalog_items')) {
            Schema::table('service_catalog_items', function (Blueprint $table) {
                $columns = [];

                if (Schema::hasColumn('service_catalog_items', 'long_description')) {
                    $columns[] = 'long_description';
                }

                if (Schema::hasColumn('service_catalog_items', 'national_tax_code')) {
                    $columns[] = 'national_tax_code';
                }

                if (Schema::hasColumn('service_catalog_items', 'nbs_code')) {
                    $columns[] = 'nbs_code';
                }

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
