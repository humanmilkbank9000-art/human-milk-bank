<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'breastmilk_requests';

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            // Appointment chosen by user
            if (!Schema::hasColumn(self::TABLE, 'scheduled_date')) {
                $table->date('scheduled_date')->nullable()->after('needed_by_date');
            }
            if (!Schema::hasColumn(self::TABLE, 'scheduled_time')) {
                $table->time('scheduled_time')->nullable()->after('scheduled_date');
            }

            // Admin-decided dispensing values
            if (!Schema::hasColumn(self::TABLE, 'decided_number_of_bags')) {
                $table->integer('decided_number_of_bags')->nullable()->after('scheduled_time');
            }
            if (!Schema::hasColumn(self::TABLE, 'decided_total_volume')) {
                $table->integer('decided_total_volume')->nullable()->after('decided_number_of_bags'); // in ml
            }

            // Allow removing these fields from user flow
            try {
                $table->integer('requested_volume')->nullable()->change();
            } catch (\Throwable $e) { /* ignore if already nullable */ }
            try {
                $table->date('needed_by_date')->nullable()->change();
            } catch (\Throwable $e) { /* ignore if already nullable */ }

            // Helpful composite index for slot lookups
            $hasIndex = false;
            try {
                // Some drivers may not expose index existence easily; attempt create safely
                $table->index(['scheduled_date', 'scheduled_time', 'status'], 'bm_requests_sched_status_idx');
            } catch (\Throwable $e) { $hasIndex = true; }
        });
    }

    public function down(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table): void {
            if (Schema::hasColumn(self::TABLE, 'decided_total_volume')) {
                $table->dropColumn('decided_total_volume');
            }
            if (Schema::hasColumn(self::TABLE, 'decided_number_of_bags')) {
                $table->dropColumn('decided_number_of_bags');
            }
            if (Schema::hasColumn(self::TABLE, 'scheduled_time')) {
                $table->dropColumn('scheduled_time');
            }
            if (Schema::hasColumn(self::TABLE, 'scheduled_date')) {
                $table->dropColumn('scheduled_date');
            }
            try {
                $table->dropIndex('bm_requests_sched_status_idx');
            } catch (\Throwable $e) { /* ignore */ }
            // Do not force requested_volume/needed_by_date back to NOT NULL to avoid data loss
        });
    }
};
