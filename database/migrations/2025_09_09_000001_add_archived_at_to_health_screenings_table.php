<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('health_screenings', 'archived_at')) {
            Schema::table('health_screenings', function (Blueprint $table) {
                $table->timestamp('archived_at')->nullable()->after('updated_at')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('health_screenings', 'archived_at')) {
            Schema::table('health_screenings', function (Blueprint $table) {
                $table->dropColumn('archived_at');
            });
        }
    }
};
