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
        if (!Schema::hasColumn(self::TABLE, 'archived_at')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->timestamp('archived_at')->nullable()->after('dispensed_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn(self::TABLE, 'archived_at')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->dropColumn('archived_at');
            });
        }
    }
};
