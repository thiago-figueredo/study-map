<?php

namespace Tests\Feature\Quiz;

use App\Enums\ReviewFeedback as ReviewFeedbackEnum;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\ReviewFeedback;
use Carbon\Carbon;
use Tests\TestCase;

class ReviewQuizTest extends TestCase
{
    private const url = '/api/reviews';
    private string $now;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = '2026-04-30 10:00:00';
        Carbon::setTestNow($this->now);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_review_quiz_again_feedback(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->withAnswer()->for($quiz)->create();
        $body = [
            'question_id' => $question->id,
            'feedback' => ReviewFeedbackEnum::AGAIN,
        ];

        $this->postJson(self::url, $body)->assertNoContent();

        $this->assertDatabaseHas(ReviewFeedback::class, [
            'question_id' => $question->id,
            'feedback' => ReviewFeedbackEnum::AGAIN,
            'easy_factor' => 2.5,
            'interval' => 1,
            'repetitions' => 1,
            'next_review_date' => Carbon::parse($this->now)->addDay()->format('Y-m-d'),
        ]);
    }

    public function test_review_quiz_hard_feedback(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->withAnswer()->for($quiz)->create();
        $body = [
            'question_id' => $question->id,
            'feedback' => ReviewFeedbackEnum::HARD,
        ];

        $this->postJson(self::url, $body)->assertNoContent();

        $this->assertDatabaseHas(ReviewFeedback::class, [
            'question_id' => $question->id,
            'feedback' => ReviewFeedbackEnum::HARD,
            'easy_factor' => 2.36,
            'interval' => 1,
            'repetitions' => 1,
            'next_review_date' => Carbon::parse($this->now)->addDay()->format('Y-m-d'),
        ]);
    }

    public function test_review_quiz_medium_feedback(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->withAnswer()->for($quiz)->create();
        $body = [
            'question_id' => $question->id,
            'feedback' => ReviewFeedbackEnum::MEDIUM,
        ];

        $this->postJson(self::url, $body)->assertNoContent();

        $this->assertDatabaseHas(ReviewFeedback::class, [
            'question_id' => $question->id,
            'feedback' => ReviewFeedbackEnum::MEDIUM,
            'easy_factor' => 2.18,
            'interval' => 1,
            'repetitions' => 1,
            'next_review_date' => Carbon::parse($this->now)->addDay()->format('Y-m-d'),
        ]);
    }

    public function test_review_quiz_easy_feedback(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->withAnswer()->for($quiz)->create();
        $this->postJson(self::url, [
            'question_id' => $question->id,
            'feedback' => ReviewFeedbackEnum::EASY,
        ])->assertNoContent();

        $this->assertDatabaseHas(ReviewFeedback::class, [
            'question_id' => $question->id,
            'feedback' => ReviewFeedbackEnum::EASY,
            'easy_factor' => 1.96,
            'interval' => 1,
            'repetitions' => 1,
            'next_review_date' => Carbon::parse($this->now)->addDay()->format('Y-m-d'),
        ]);
    }
}
