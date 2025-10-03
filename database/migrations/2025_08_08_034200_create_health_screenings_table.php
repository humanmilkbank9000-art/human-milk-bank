<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table names.
     */
    private const HEALTH_SCREENINGS_TABLE = 'health_screenings';
    private const MEDICAL_HISTORY_ANSWERS_TABLE = 'medical_history_answers';
    private const SEXUAL_HISTORY_ANSWERS_TABLE = 'sexual_history_answers';
    private const DONOR_INFANT_ANSWERS_TABLE = 'donor_infant_answers';
    private const NOTIFICATIONS_TABLE = 'notifications';
    private const USERS_TABLE = 'users';

    /**
     * Donor type options.
     */
    private const DONOR_TYPES = ['community', 'private', 'employee', 'network_office_agency'];

    /**
     * Status options.
     */
    private const STATUS_OPTIONS = ['pending', 'accepted', 'declined'];

    /**
     * Answer options.
     */
    private const ANSWER_OPTIONS = ['yes', 'no'];

    /**
     * Notification types.
     */
    private const NOTIFICATION_TYPES = ['health_screening_submitted', 'health_screening_result'];

    /**
     * Run the migrations.
     *
     * Creates the health screening system tables including health screenings,
     * medical history answers, sexual history answers, donor infant answers,
     * and notifications.
     */
    public function up(): void
    {
        $this->createHealthScreeningsTable();
        $this->createMedicalHistoryAnswersTable();
        $this->createSexualHistoryAnswersTable();
        $this->createDonorInfantAnswersTable();
        $this->createNotificationsTable();
    }

    /**
     * Create the main health screenings table.
     */
    private function createHealthScreeningsTable(): void
    {
        Schema::create(self::HEALTH_SCREENINGS_TABLE, function (Blueprint $table): void {
            $table->id('Health_Screening_ID');
            $table->unsignedBigInteger('User_ID')->comment('Reference to user submitting the screening');
            $table->string('civil_status')->comment('Civil status of the donor');
            $table->string('occupation')->comment('Occupation of the donor');
            $table->enum('type_of_donor', self::DONOR_TYPES)->comment('Type of donor classification');
            $table->enum('status', self::STATUS_OPTIONS)->default('pending')->comment('Current screening status');
            $table->text('admin_notes')->nullable()->comment('Administrative notes about the screening');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('User_ID')
                ->references('User_ID')
                ->on(self::USERS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['status', 'created_at']);
            $table->index('User_ID');
        });
    }

    /**
     * Create medical history answers table.
     */
    private function createMedicalHistoryAnswersTable(): void
    {
        Schema::create(self::MEDICAL_HISTORY_ANSWERS_TABLE, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('health_screening_id')->comment('Reference to health screening');
            $table->integer('question_number')->comment('Question numbers 1-15');
            $table->enum('answer', self::ANSWER_OPTIONS)->comment('Yes/No answer to the question');
            $table->text('additional_info')->nullable()->comment('Follow-up details when answer is yes');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('health_screening_id')
                ->references('Health_Screening_ID')
                ->on(self::HEALTH_SCREENINGS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['health_screening_id', 'question_number'], 'medical_history_screening_question_idx');
        });
    }

    /**
     * Create sexual history answers table.
     */
    private function createSexualHistoryAnswersTable(): void
    {
        Schema::create(self::SEXUAL_HISTORY_ANSWERS_TABLE, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('health_screening_id')->comment('Reference to health screening');
            $table->integer('question_number')->comment('Question numbers 1-4');
            $table->enum('answer', self::ANSWER_OPTIONS)->comment('Yes/No answer to the question');
            $table->text('additional_info')->nullable()->comment('Follow-up details when answer is yes');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('health_screening_id')
                ->references('Health_Screening_ID')
                ->on(self::HEALTH_SCREENINGS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['health_screening_id', 'question_number'], 'sexual_history_screening_question_idx');
        });
    }

    /**
     * Create donor infant answers table.
     */
    private function createDonorInfantAnswersTable(): void
    {
        Schema::create(self::DONOR_INFANT_ANSWERS_TABLE, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('health_screening_id')->comment('Reference to health screening');
            $table->integer('question_number')->comment('Question numbers 1-5');
            $table->enum('answer', self::ANSWER_OPTIONS)->comment('Yes/No answer to the question');
            $table->text('additional_info')->nullable()->comment('Follow-up details when answer is yes');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('health_screening_id')
                ->references('Health_Screening_ID')
                ->on(self::HEALTH_SCREENINGS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['health_screening_id', 'question_number'], 'donor_infant_screening_question_idx');
        });
    }

    /**
     * Create notifications table for health screening updates.
     */
    private function createNotificationsTable(): void
    {
        Schema::create(self::NOTIFICATIONS_TABLE, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('User_ID')->nullable()->comment('Reference to user receiving notification');
            $table->unsignedBigInteger('health_screening_id')->nullable()->comment('Reference to related health screening');
            $table->string('type')->comment('Notification type: health_screening_submitted, health_screening_result');
            $table->string('title')->comment('Notification title');
            $table->text('message')->comment('Notification message content');
            $table->boolean('is_read')->default(false)->comment('Whether notification has been read');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('User_ID')
                ->references('User_ID')
                ->on(self::USERS_TABLE)
                ->onDelete('cascade');

            $table->foreign('health_screening_id')
                ->references('Health_Screening_ID')
                ->on(self::HEALTH_SCREENINGS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['User_ID', 'is_read', 'created_at'], 'notifications_user_read_created_idx');
            $table->index('type', 'notifications_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops all health screening system tables in the correct order
     * to avoid foreign key constraint violations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::NOTIFICATIONS_TABLE);
        Schema::dropIfExists(self::DONOR_INFANT_ANSWERS_TABLE);
        Schema::dropIfExists(self::SEXUAL_HISTORY_ANSWERS_TABLE);
        Schema::dropIfExists(self::MEDICAL_HISTORY_ANSWERS_TABLE);
        Schema::dropIfExists(self::HEALTH_SCREENINGS_TABLE);
    }
};
