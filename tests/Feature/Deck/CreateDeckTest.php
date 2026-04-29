<?php

namespace Tests\Feature\Deck;

use App\Http\Resources\DeckResource;
use App\Models\Answer;
use App\Models\Deck;
use App\Models\Question;
use Illuminate\Support\Arr;
use Tests\TestCase;

class CreateDeckTest extends TestCase
{
    const url = '/api/decks';

    public function test_create_deck_only(): void
    {
        $body = ['name' => 'Test Deck'];

        $this->postJson(self::url, $body)
            ->assertCreated()
            ->assertJson($this->expectedJson($body))
            ->assertJsonStructure(['data' => DeckResource::jsonStructure()]);

        $this->assertDatabaseHasOne(Deck::class, $body);
    }

    public function test_create_deck_with_questions_without_answers(): void
    {
        $body = [
            'name' => 'Test Deck',
            'questions' => [
                ['body' => 'What is the capital of France?'],
                ['body' => 'What is the capital of Germany?'],
            ]
        ];

        $this->postJson(self::url, $body)
            ->assertCreated()
            ->assertJson($this->expectedJson($body))
            ->assertJsonStructure(['data' => DeckResource::jsonStructure()]);

        $this->assertDatabaseHasOne(Deck::class, Arr::except($body, ['questions']));
        $this->assertDatabaseHasMany(Question::class, $body['questions']);
    }

    public function test_create_deck_with_questions_and_multiple_answers(): void
    {   
        $body = [
            'name' => 'Test Deck',
            'questions' => [
                [
                    'body' => 'What is the capital of France?',
                    'answers' => [
                        ['body' => 'Paris', 'is_correct' => true],
                        ['body' => 'Rio de Janeiro', 'is_correct' => false]
                    ]
                ],
                [
                    'body' => 'What is the capital of Germany?',
                    'answers' => [
                        ['body' => 'Berlin', 'is_correct' => true],
                        ['body' => 'Madrid', 'is_correct' => false]
                    ]
                ]
            ]
        ];

        $response = $this->postJson(self::url, $body)
            ->assertCreated()
            ->assertJson($this->expectedJson($body))
            ->assertJsonStructure(['data' => DeckResource::jsonStructure()]);

        $this->assertDatabaseHasOne(Deck::class, Arr::except($body, ['questions']));

        $this->assertDatabaseHasMany(
            Question::class,
            collect($body['questions'])->map(fn ($q) => Arr::only($q, ['body']))->toArray()
        );

        $this->assertDatabaseHasMany(
            Answer::class,
            collect($body['questions'])
                ->flatMap(function ($question) use ($response) {
                    return collect($question['answers'])->map(fn ($answer) => [
                        ...$answer,
                        'question_id' => collect($response->json('data.questions'))
                            ->firstWhere('body', $question['body'])
                            ['id'],
                    ]);
                })
                ->toArray()
        );
    }

    private function expectedJson(array $body): array
    {
        return [
            'data' => [
                'name' => $body['name'],
                'questions' => isset($body['questions']) ? $body['questions'] : null,
            ],
        ];
    }
}