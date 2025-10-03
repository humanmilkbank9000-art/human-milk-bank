<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'pasteurized_inventory';
    private const INDEX = 'pasteurized_inventory_batch_number_unique';

    public function up(): void
    {
        if (Schema::hasTable(self::TABLE) && Schema::hasColumn(self::TABLE, 'batch_number')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                try { $table->dropUnique(self::INDEX); } catch (\Throwable $e) { /* index may not exist; ignore */ }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable(self::TABLE) && Schema::hasColumn(self::TABLE, 'batch_number')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                try { $table->unique('batch_number', self::INDEX); } catch (\Throwable $e) { /* ignore */ }
            });
        }
    }
};
