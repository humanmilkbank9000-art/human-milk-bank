<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorInfantAnswer extends Model
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
     * Get the health screening that owns the donor infant answer.
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
            1 => "Is your child healthy?",
            2 => "Was your child delivered full term?",
            3 => "Are you exclusively breastfeeding your child?",
            4 => "Is/was your youngest child jaundiced?",
            5 => "Have you ever received breastmilk from another mother?"
        ];

        return $questions[$this->question_number] ?? "Unknown question";
    }

    /**
     * Get the Bisaya translation for this question.
     */
    public function getQuestionBisayaAttribute()
    {
        $bisayaQuestions = [
            1 => "Himsog ba ang imung anak?",
            2 => "Gipanganak ba siya sa saktong buwan?",
            3 => "Kaugalingong gatas lang ba nimu ang gipalnum sa bata?",
            4 => "imung kinamanghuran na bata ba niagi og pagdalag sa pamanit?",
            5 => "Nakadawat ba ang imung anak og gatas sa laing inahan?"
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
