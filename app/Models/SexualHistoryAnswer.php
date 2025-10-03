<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SexualHistoryAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'health_screening_id',
        'question_number',
        'answer',
        'additional_info'
    ];

    protected $casts = [
        'question_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the health screening that owns the sexual history answer.
     */
    public function healthScreening()
    {
        return $this->belongsTo(HealthScreening::class, 'health_screening_id', 'Health_Screening_ID');
    }

    /**
     * Get the question text for this answer.
     */
    public function getQuestionTextAttribute()
    {
        $questions = [
            1 => "Have you ever had Syphilis, HIV, herpes or any sexually transmitted disease (STD)?",
            2 => "Do you have multiple sexual partners?",
            3 => "Have you had a sexual partner who is: Bisexual, Promiscuous, Has had an STD/AIDS/HIV, Received blood for bleeding problems, or Is an intravenous drug user?",
            4 => "Have you had a tattoo applied or had an accidental needlestick injury or contact with someone else's blood?"
        ];

        return $questions[$this->question_number] ?? "Unknown question";
    }

    /**
     * Get the Bisaya translation for this question.
     */
    public function getQuestionBisayaAttribute()
    {
        $bisayaQuestions = [
            1 => "Niagi ka ba og bisan unsang sakit sa kinatawo? ○ sakit na makuha pinaagi sa pakighilawas?",
            2 => "aduna ka bay lain pares sa pakighilawas gawas sa imu bana/kapikas?",
            3 => "Niagi ka ba og pakighilawas ning mga mosunod? Bisexual (silahis), Promiscuous (bisan kinsa ang pares), Has had an STD, AIDS/HIV (adunay sakit sa kinatawo), Received blood for a long period of time for a bleeding problem (niagi og abuno sa dugo), Is an intravenous drug user (gagamit og bisan unsang druga pinaagi sa pagtusok sa dagum sa ugat)",
            4 => "Niagi ka ba og papatik sukad? Niagi ka ba og katusok sa bisan unsang dagom?"
        ];

        return $bisayaQuestions[$this->question_number] ?? "Unknown question";
    }

    /**
     * Scope a query to only include answers for a specific question.
     */
    public function scopeForQuestion($query, $questionNumber)
    {
        return $query->where('question_number', $questionNumber);
    }

    /**
     * Scope a query to only include 'yes' answers.
     */
    public function scopeYesAnswers($query)
    {
        return $query->where('answer', 'yes');
    }

    /**
     * Scope a query to only include 'no' answers.
     */
    public function scopeNoAnswers($query)
    {
        return $query->where('answer', 'no');
    }
}
