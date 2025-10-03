<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		if (Schema::hasTable('notifications')) {
			return; // Already created by another migration
		}

		Schema::create('notifications', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('User_ID')->nullable()->index();
			$table->string('type')->nullable();
			$table->string('title');
			$table->text('message');
			$table->boolean('is_read')->default(false)->index();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('notifications');
	}
};


