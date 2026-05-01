<?php

namespace Tests\Feature\Quiz;

use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Tests\TestCase;

class ListQuizzesTest extends TestCase
{
    const url = '/api/quizzes';

    public function test_list_quizzes(): void
    {
        $quizzes = Quiz::factory(3)->create();

        $this->getJson(self::url)
            ->assertOk()
            ->assertJsonCount($quizzes->count(), 'data')
            ->assertJson(['data' => QuizResource::collection($quizzes)->resolve()]);
    }
}
