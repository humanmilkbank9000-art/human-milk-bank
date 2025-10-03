<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\HealthScreening;
use App\Models\MedicalHistoryAnswer;
use App\Models\SexualHistoryAnswer;
use App\Models\DonorInfantAnswer;

class UnifiedHealthScreeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::create([
            'Contact_Number' => '09123456789',
            'Full_Name' => 'Test User',
            'Age' => 25,
            'Address' => 'Test Address',
            'User_Type' => 'donor',
            'Password' => bcrypt('password'),
            'Date_Of_Birth' => '1999-01-01',
            'Sex' => 'Female'
        ]);
    }

    /** @test */
    public function it_can_create_health_screening_with_all_answers()
    {
        // Create health screening
        $screening = HealthScreening::create([
            'User_ID' => $this->user->User_ID,
            'civil_status' => 'Single',
            'occupation' => 'Teacher',
            'type_of_donor' => 'community',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('health_screenings', [
            'Health_Screening_ID' => $screening->Health_Screening_ID,
            'User_ID' => $this->user->User_ID,
            'status' => 'pending'
        ]);

        // Create medical history answers
        for ($i = 1; $i <= 15; $i++) {
            MedicalHistoryAnswer::create([
                'health_screening_id' => $screening->Health_Screening_ID,
                'question_number' => $i,
                'answer' => $i % 2 == 0 ? 'yes' : 'no',
                'additional_info' => $i == 2 ? 'Test additional info' : null
            ]);
        }

        // Create sexual history answers
        for ($i = 1; $i <= 4; $i++) {
            SexualHistoryAnswer::create([
                'health_screening_id' => $screening->Health_Screening_ID,
                'question_number' => $i,
                'answer' => 'no',
                'additional_info' => null
            ]);
        }

        // Create donor infant answers
        for ($i = 1; $i <= 5; $i++) {
            DonorInfantAnswer::create([
                'health_screening_id' => $screening->Health_Screening_ID,
                'question_number' => $i,
                'answer' => 'yes',
                'additional_info' => null
            ]);
        }

        // Verify all answers were created
        $this->assertEquals(15, $screening->medicalHistoryAnswers()->count());
        $this->assertEquals(4, $screening->sexualHistoryAnswers()->count());
        $this->assertEquals(5, $screening->donorInfantAnswers()->count());
    }

    /** @test */
    public function it_can_load_screening_with_relationships()
    {
        // Create screening with answers
        $screening = HealthScreening::create([
            'User_ID' => $this->user->User_ID,
            'civil_status' => 'Married',
            'occupation' => 'Nurse',
            'type_of_donor' => 'private',
            'status' => 'accepted'
        ]);

        // Add some answers
        MedicalHistoryAnswer::create([
            'health_screening_id' => $screening->Health_Screening_ID,
            'question_number' => 1,
            'answer' => 'yes',
            'additional_info' => null
        ]);

        // Load with relationships
        $loadedScreening = HealthScreening::with([
            'user',
            'medicalHistoryAnswers',
            'sexualHistoryAnswers',
            'donorInfantAnswers'
        ])->find($screening->Health_Screening_ID);

        $this->assertNotNull($loadedScreening);
        $this->assertEquals($this->user->User_ID, $loadedScreening->user->User_ID);
        $this->assertEquals(1, $loadedScreening->medicalHistoryAnswers->count());
    }

    /** @test */
    public function medical_history_answer_provides_question_text_and_bisaya()
    {
        $screening = HealthScreening::create([
            'User_ID' => $this->user->User_ID,
            'civil_status' => 'Single',
            'occupation' => 'Teacher',
            'type_of_donor' => 'community',
            'status' => 'pending'
        ]);

        $answer = MedicalHistoryAnswer::create([
            'health_screening_id' => $screening->Health_Screening_ID,
            'question_number' => 1,
            'answer' => 'no',
            'additional_info' => null
        ]);

        $this->assertEquals("Have you donated breastmilk before?", $answer->question_text);
        $this->assertEquals("Nakahatag ka na ba ug gatas sa inahan kaniadto?", $answer->question_bisaya);
    }

    /** @test */
    public function it_can_scope_screenings_by_status()
    {
        // Create screenings with different statuses
        HealthScreening::create([
            'User_ID' => $this->user->User_ID,
            'civil_status' => 'Single',
            'occupation' => 'Teacher',
            'type_of_donor' => 'community',
            'status' => 'pending'
        ]);

        HealthScreening::create([
            'User_ID' => $this->user->User_ID,
            'civil_status' => 'Married',
            'occupation' => 'Nurse',
            'type_of_donor' => 'private',
            'status' => 'accepted'
        ]);

        $this->assertEquals(1, HealthScreening::pending()->count());
        $this->assertEquals(1, HealthScreening::accepted()->count());
        $this->assertEquals(0, HealthScreening::declined()->count());
    }
}
