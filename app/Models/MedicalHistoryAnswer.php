<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Medical History Answer Model
 *
 * Represents answers to medical history questions in health screenings.
 * Contains 15 standardized medical history questions with English and Bisaya translations.
 *
 * @property int $id
 * @property int $health_screening_id
 * @property int $question_number
 * @property string $answer
 * @property string|null $additional_info
 */
class MedicalHistoryAnswer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'health_screening_id',
        'question_number',
        'answer',
        'additional_info',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'question_number' => 'integer',
        'health_screening_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Answer constants.
     */
    public const ANSWER_YES = 'yes';
    public const ANSWER_NO = 'no';

    /**
     * Question count constant.
     */
    public const TOTAL_QUESTIONS = 15;

    /**
     * Medical history questions in English.
     */
    private const QUESTIONS_ENGLISH = [
        1 => 'Have you donated breastmilk before?',
        2 => 'Have you for any reason been deferred as a breastmilk donor?',
        3 => 'Did you have a normal pregnancy and delivery for your most recent pregnancy?',
        4 => 'Do you have any acute or chronic infection such as but not limited to: tuberculosis, hepatitis, systemic disorders?',
        5 => 'Have you been diagnosed with a chronic non-infectious illness such as but not limited to: diabetes, hypertension, heart disease?',
        6 => 'Have you received any blood transfusion or any blood products within the last twelve (12) months?',
        7 => 'Have you received any organ or tissue transplant within the last twelve (12) months?',
        8 => 'Have you had any intake of alcohol or hard liquor within the last twenty four (24) hours?',
        9 => 'Do you use megadose vitamins or pharmacologically active herbal preparations?',
        10 => 'Do you regularly use over-the-counter medications or systemic preparations such as replacement hormones, birth control hormones, anti-hypoglycemics, blood thinners?',
        11 => 'Are you a total vegetarian/vegan?',
        12 => 'Do you use illicit drugs?',
        13 => 'Do you smoke?',
        14 => 'Are you around people who smoke (passive smoking)?',
        15 => 'Have you had breast augmentation surgery, using silicone breast implants?',
    ];

    /**
     * Medical history questions in Bisaya.
     */
    private const QUESTIONS_BISAYA = [
        1 => 'Nakahatag/naka-donar ka na ba sa imung gatas kaniadto?',
        2 => 'Naballbaran na ba ka nga mag-donar sa imung gatas kaniadto?',
        3 => 'Wala ka bay naaging mnga kalisod og komplikasyon sa pinakaulahi nimung pagburos og pagpanganak?',
        4 => 'Aduna ka bay gibating mga sakit sama sa Tuberculosis, sakit sa atay or sakit sa dugo?',
        5 => 'Nadayagnos ka ba nga adunay laygay nga dll makatakod nga sakit sama sa apas dill limitado sa: altapresyon, sakit sa kasingkasing',
        6 => 'Naabunohan ka ba ug dugo sulod sa niaging 12 ka buwan?',
        7 => 'Niagi ka ba ug operasyon din nidawat ka ug bahin/parte sa lawas sulod sa nlilabay nga 12 ka bulan?',
        8 => 'Sulod sa 24 oras, naka inum ka ba og bisan unsang ilimnong makahubog?',
        9 => 'Gainum ka ba og sobra sa gitakda na mga bitamina og mga produktong adunay sagol na herbal?',
        10 => 'Kanunay ba ka gagamit o gainum sa mga tambal kung lain ang paminaw sa lawas? Og gainum ka ba sa mha tambal pampugong sa pagburos?',
        11 => 'Ikaw ba dili gakaon sa lain pagkaon kundi utan lang?',
        12 => 'Gagamit ka ba sa ginadilina mga droga?',
        13 => 'Gapanigarilyo ka ba?',
        14 => 'doul ba ka permi sa mga tao nga gapanigarilyo?',
        15 => 'kaw ba niagi ug operasyon sa imung suso din nagpabutang ug "silicone" O artipisyal na suso?',
    ];

    /**
     * Get the health screening that owns the medical history answer.
     */
    public function healthScreening(): BelongsTo
    {
        return $this->belongsTo(HealthScreening::class, 'health_screening_id', 'Health_Screening_ID');
    }

    /**
     * Get the question text in English for this answer.
     */
    public function getQuestionTextAttribute(): string
    {
        return self::QUESTIONS_ENGLISH[$this->question_number] ?? 'Unknown question';
    }

    /**
     * Get the question text in Bisaya for this answer.
     */
    public function getQuestionBisayaAttribute(): string
    {
        return self::QUESTIONS_BISAYA[$this->question_number] ?? 'Unknown question';
    }

    /**
     * Scope a query to only include answers for a specific question.
     */
    public function scopeForQuestion(Builder $query, int $questionNumber): Builder
    {
        return $query->where('question_number', $questionNumber);
    }

    /**
     * Scope a query to only include 'yes' answers.
     */
    public function scopeYesAnswers(Builder $query): Builder
    {
        return $query->where('answer', self::ANSWER_YES);
    }

    /**
     * Scope a query to only include 'no' answers.
     */
    public function scopeNoAnswers(Builder $query): Builder
    {
        return $query->where('answer', self::ANSWER_NO);
    }

    /**
     * Scope a query to filter by answer type.
     */
    public function scopeByAnswer(Builder $query, string $answer): Builder
    {
        return $query->where('answer', $answer);
    }

    /**
     * Check if the answer is 'yes'.
     */
    public function isYes(): bool
    {
        return $this->answer === self::ANSWER_YES;
    }

    /**
     * Check if the answer is 'no'.
     */
    public function isNo(): bool
    {
        return $this->answer === self::ANSWER_NO;
    }

    /**
     * Check if the answer has additional information.
     */
    public function hasAdditionalInfo(): bool
    {
        return !empty($this->additional_info);
    }

    /**
     * Get all available answer options.
     */
    public static function getAnswerOptions(): array
    {
        return [
            self::ANSWER_YES,
            self::ANSWER_NO,
        ];
    }

    /**
     * Get all medical history questions in English.
     */
    public static function getQuestionsEnglish(): array
    {
        return self::QUESTIONS_ENGLISH;
    }

    /**
     * Get all medical history questions in Bisaya.
     */
    public static function getQuestionsBisaya(): array
    {
        return self::QUESTIONS_BISAYA;
    }

    /**
     * Get a specific question in English by number.
     */
    public static function getQuestionEnglish(int $questionNumber): string
    {
        return self::QUESTIONS_ENGLISH[$questionNumber] ?? 'Unknown question';
    }

    /**
     * Get a specific question in Bisaya by number.
     */
    public static function getQuestionBisaya(int $questionNumber): string
    {
        return self::QUESTIONS_BISAYA[$questionNumber] ?? 'Unknown question';
    }
}
