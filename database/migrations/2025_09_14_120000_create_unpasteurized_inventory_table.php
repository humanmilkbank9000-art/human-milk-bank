<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	private const TABLE = 'unpasteurized_inventory';
	private const DONATION_TABLE = 'donation_history';
	private const USERS_TABLE = 'users';

	public function up(): void
	{
		Schema::create(self::TABLE, function (Blueprint $table): void {
			$table->id();
			$table->unsignedBigInteger('donation_id')->comment('FK to donation_history.id');
			$table->unsignedBigInteger('User_ID')->comment('FK to users.User_ID');
			$table->integer('number_of_bags')->nullable();
			$table->decimal('total_volume', 8, 2)->nullable();
			$table->date('date_received')->nullable();
			$table->time('time_received')->nullable();
			$table->timestamps();

			$table->foreign('donation_id')->references('id')->on(self::DONATION_TABLE)->onDelete('cascade');
			$table->foreign('User_ID')->references('User_ID')->on(self::USERS_TABLE)->onDelete('cascade');
			$table->index(['User_ID','date_received']);
		});

		// Backfill from completed, non-archived donations
		try {
			$existing = DB::table(self::DONATION_TABLE)
				->where('status','completed')
				->whereNull('archived_at')
				->select('id','User_ID','number_of_bags','total_volume','donation_date','donation_time')
				->get();

			foreach ($existing as $row) {
				DB::table(self::TABLE)->insert([
					'donation_id' => $row->id,
					'User_ID' => $row->User_ID,
					'number_of_bags' => $row->number_of_bags,
					'total_volume' => $row->total_volume,
					'date_received' => $row->donation_date,
					'time_received' => $row->donation_time,
					'created_at' => now(),
					'updated_at' => now(),
				]);
			}
		} catch (\Throwable $e) {
			// Ignore backfill errors to not block migration
		}
	}

	public function down(): void
	{
		Schema::dropIfExists(self::TABLE);
	}
};

