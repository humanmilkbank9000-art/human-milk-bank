<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('unpasteurized_inventory')) {
            return;
        }

        Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
            if (!Schema::hasColumn('unpasteurized_inventory', 'donation_id')) {
                $table->unsignedBigInteger('donation_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('unpasteurized_inventory', 'User_ID')) {
                $table->unsignedBigInteger('User_ID')->nullable()->after('donation_id');
            }
        });

        // Add foreign keys if possible (wrap in try to avoid failures on existing constraints)
        try {
            Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
                // Adding FK only if columns exist and no FK yet; Laravel doesn't expose FK existence easily so we try-catch
                $table->foreign('donation_id')->references('id')->on('donation_history')->onDelete('cascade');
            });
        } catch (\Throwable $e) { /* ignore */ }

        try {
            Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
                $table->foreign('User_ID')->references('User_ID')->on('users')->onDelete('cascade');
            });
        } catch (\Throwable $e) { /* ignore */ }

        // Ensure unique index on donation_id exists
        try {
            $exists = DB::table('information_schema.statistics')
                ->select('INDEX_NAME')
                ->whereRaw('TABLE_SCHEMA = DATABASE()')
                ->where('TABLE_NAME', 'unpasteurized_inventory')
                ->where('INDEX_NAME', 'unpasteurized_inventory_donation_id_unique')
                ->exists();

            if (!$exists && Schema::hasColumn('unpasteurized_inventory', 'donation_id')) {
                Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
                    $table->unique('donation_id', 'unpasteurized_inventory_donation_id_unique');
                });
            }
        } catch (\Throwable $e) { /* ignore */ }
    }

    public function down(): void
    {
        if (!Schema::hasTable('unpasteurized_inventory')) {
            return;
        }
        // Drop unique and FKs then columns
        try {
            Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
                try { $table->dropUnique('unpasteurized_inventory_donation_id_unique'); } catch (\Throwable $e) {}
                try { $table->dropForeign(['donation_id']); } catch (\Throwable $e) {}
                try { $table->dropForeign(['User_ID']); } catch (\Throwable $e) {}
            });
        } catch (\Throwable $e) { /* ignore */ }

        Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
            if (Schema::hasColumn('unpasteurized_inventory', 'donation_id')) {
                $table->dropColumn('donation_id');
            }
            // Do not drop User_ID if relied upon elsewhere; comment out for safety
            // if (Schema::hasColumn('unpasteurized_inventory', 'User_ID')) {
            //     $table->dropColumn('User_ID');
            // }
        });
    }
};
