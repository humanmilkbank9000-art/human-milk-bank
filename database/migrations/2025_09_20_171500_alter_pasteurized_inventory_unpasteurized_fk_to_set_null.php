<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'pasteurized_inventory';
    private const UNPASTEURIZED_TABLE = 'unpasteurized_inventory';

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            // Drop existing FK to unpasteurized (likely cascade)
            $table->dropForeign([ 'unpasteurized_id' ]);
        });

        Schema::table(self::TABLE, function (Blueprint $table): void {
            // Make column nullable then add FK with SET NULL on delete
            $table->unsignedBigInteger('unpasteurized_id')->nullable()->change();
            $table->foreign('unpasteurized_id')
                ->references('id')
                ->on(self::UNPASTEURIZED_TABLE)
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->dropForeign([ 'unpasteurized_id' ]);
        });

        Schema::table(self::TABLE, function (Blueprint $table): void {
            // Revert to NOT NULL and cascade delete
            $table->unsignedBigInteger('unpasteurized_id')->nullable(false)->change();
            $table->foreign('unpasteurized_id')
                ->references('id')
                ->on(self::UNPASTEURIZED_TABLE)
                ->onDelete('cascade');
        });
    }
};
