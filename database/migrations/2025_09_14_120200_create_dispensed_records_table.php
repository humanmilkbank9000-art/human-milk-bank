<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'dispensed_records';
    private const UNPASTEURIZED_TABLE = 'unpasteurized_inventory';
    private const PASTEURIZED_TABLE = 'pasteurized_inventory';

    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table): void {
            $table->id();
            // Depending on source, one of these FKs may be set
            $table->unsignedBigInteger('unpasteurized_id')->nullable()->comment('FK to unpasteurized_inventory.id');
            $table->unsignedBigInteger('pasteurized_id')->nullable()->comment('FK to pasteurized_inventory.id');
            $table->string('guardian_name')->nullable();
            $table->string('recipient_name')->nullable();
            $table->decimal('volume', 8, 2); // in ml
            $table->date('date_dispensed');
            $table->time('time_dispensed')->nullable();
            $table->timestamps();

            $table->foreign('unpasteurized_id')->references('id')->on(self::UNPASTEURIZED_TABLE)->onDelete('set null');
            $table->foreign('pasteurized_id')->references('id')->on(self::PASTEURIZED_TABLE)->onDelete('set null');
            $table->index(['date_dispensed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
