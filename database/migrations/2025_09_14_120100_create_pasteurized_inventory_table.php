<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'pasteurized_inventory';
    private const UNPASTEURIZED_TABLE = 'unpasteurized_inventory';

    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('unpasteurized_id')->comment('FK to unpasteurized_inventory.id');
            $table->string('batch_number')->unique();
            $table->integer('number_of_bags')->nullable();
            $table->decimal('total_volume', 8, 2)->nullable();
            $table->date('date_pasteurized')->nullable();
            $table->time('time_pasteurized')->nullable();
            $table->timestamps();

            $table->foreign('unpasteurized_id')->references('id')->on(self::UNPASTEURIZED_TABLE)->onDelete('cascade');
            $table->index(['date_pasteurized']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
