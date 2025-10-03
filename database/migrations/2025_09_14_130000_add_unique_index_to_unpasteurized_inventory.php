<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('unpasteurized_inventory') && Schema::hasColumn('unpasteurized_inventory', 'donation_id')) {
            Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
                // Ensure each donation produces at most one inventory row
                $table->unique('donation_id', 'unpasteurized_inventory_donation_id_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('unpasteurized_inventory')) {
            try {
                Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
                    $table->dropUnique('unpasteurized_inventory_donation_id_unique');
                });
            } catch (\Throwable $e) {
                // ignore if not exists
            }
        }
    }
};
