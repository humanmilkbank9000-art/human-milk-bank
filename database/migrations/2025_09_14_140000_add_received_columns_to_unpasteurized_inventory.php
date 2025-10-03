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
            return; // nothing to do
        }

        Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
            if (!Schema::hasColumn('unpasteurized_inventory', 'date_received')) {
                $table->date('date_received')->nullable()->after('total_volume');
            }
            if (!Schema::hasColumn('unpasteurized_inventory', 'time_received')) {
                $table->time('time_received')->nullable()->after('date_received');
            }
        });

        // Backfill newly added columns using donation_history if possible
        try {
            // MySQL-compatible update with join
            DB::statement(
                "UPDATE unpasteurized_inventory ui 
                 INNER JOIN donation_history dh ON ui.donation_id = dh.id 
                 SET 
                   ui.date_received = COALESCE(dh.donation_date, dh.scheduled_date),
                   ui.time_received = COALESCE(dh.donation_time, dh.scheduled_time)
                 WHERE (ui.date_received IS NULL OR ui.time_received IS NULL)"
            );
        } catch (\Throwable $e) {
            // As a fallback, do a PHP-side backfill for rows still NULL
            try {
                $rows = DB::table('unpasteurized_inventory')->whereNull('date_received')->orWhereNull('time_received')->get(['id','donation_id']);
                foreach ($rows as $row) {
                    $dh = DB::table('donation_history')->where('id',$row->donation_id)->first(['donation_date','donation_time','scheduled_date','scheduled_time']);
                    if ($dh) {
                        DB::table('unpasteurized_inventory')->where('id',$row->id)->update([
                            'date_received' => $dh->donation_date ?: $dh->scheduled_date,
                            'time_received' => $dh->donation_time ?: $dh->scheduled_time,
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (\Throwable $e2) {
                // swallow - do not break migration
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('unpasteurized_inventory')) {
            return;
        }
        Schema::table('unpasteurized_inventory', function (Blueprint $table): void {
            if (Schema::hasColumn('unpasteurized_inventory', 'time_received')) {
                $table->dropColumn('time_received');
            }
            if (Schema::hasColumn('unpasteurized_inventory', 'date_received')) {
                $table->dropColumn('date_received');
            }
        });
    }
};
