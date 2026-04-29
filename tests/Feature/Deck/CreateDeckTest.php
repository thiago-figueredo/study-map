<?php

namespace Tests\Feature\Deck;

use App\Http\Resources\DeckResource;
use App\Models\Answer;
use App\Models\Deck;
use App\Models\Question;
use App\Models\Tag;
use App\Models\TagBind;
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
            ->assertJsonStructure(['data' => DeckResource::jsonStructure()])
            ->assertJson([
                'data' => [
                    'name' => $body['name'],
                    'questions' => [],
                    'tags' => [],
                ]
            ]);

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
            ->assertJsonStructure(['data' => DeckResource::jsonStructure()])
            ->assertJson([
                'data' => [
                    'name' => $body['name'],
                    'questions' => $body['questions'],
                    'tags' => [],
                ]
            ]);

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
            ->assertJsonStructure(['data' => DeckResource::jsonStructure()])
            ->assertJson([
                'data' => [
                    'name' => $body['name'],
                    'questions' => [
                        [
                            'body' => $body['questions'][0]['body'],
                            'answers' => $body['questions'][0]['answers'],
                            'tags' => [],
                        ],
                        [
                            'body' => $body['questions'][1]['body'],
                            'answers' => $body['questions'][1]['answers'],
                            'tags' => [],
                        ],
                    ],
                ]
            ]);

        $this->assertDatabaseHasOne(Deck::class, Arr::except($body, ['questions']));

        $this->assertDatabaseHasMany(
            Question::class,
            collect($body['questions'])->map(fn ($q) => Arr::only($q, ['body']))->toArray()
        );

        $this->assertDatabaseHasMany(
            Answer::class,
            [
                [
                    'body' => $body['questions'][0]['answers'][0]['body'],
                    'is_correct' => $body['questions'][0]['answers'][0]['is_correct'],
                    'question_id' => $response->json('data.questions.0.id'),
                ],
                [
                    'body' => $body['questions'][0]['answers'][1]['body'],
                    'is_correct' => $body['questions'][0]['answers'][1]['is_correct'],
                    'question_id' => $response->json('data.questions.0.id'),
                ],
                [
                    'body' => $body['questions'][1]['answers'][0]['body'],
                    'is_correct' => $body['questions'][1]['answers'][0]['is_correct'],
                    'question_id' => $response->json('data.questions.1.id'),
                ],
                [
                    'body' => $body['questions'][1]['answers'][1]['body'],
                    'is_correct' => $body['questions'][1]['answers'][1]['is_correct'],
                    'question_id' => $response->json('data.questions.1.id'),
                ],
            ]
        );
    }

    public function test_create_deck_with_tags(): void
    {
        $body = [
            'name' => 'Test Deck',
            'tags' => ['Tag 1', 'Tag 2'],
        ];
        
        $response = $this->postJson(self::url, $body)
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'name' => $body['name'],
                    'tags' => collect($body['tags'])
                        ->map(fn ($name) => compact('name'))
                        ->toArray(),
                ]
            ])
            ->assertJsonStructure(['data' => DeckResource::jsonStructure()]);

        $this->assertDatabaseHasOne(Deck::class, Arr::except($body, ['tags']));

        $this->assertDatabaseHasMany(
            Tag::class,
            collect($body['tags'])->map(fn (string $name) => compact('name'))->toArray()
        );

        $this->assertDatabaseHasMany(
            TagBind::class,
            collect($body['tags'])
                ->map(fn (string $tag) => [
                    'tag_id' => Tag::where('name', $tag)->value('id'),
                    'binded_id' => $response->json('data.id'),
                    'binded_type' => Deck::class,
                ])
                ->toArray()
        );
    }

    public function test_create_deck_with_tagged_questions(): void
    {
        $body = [
            'name' => 'Test Deck',
            'questions' => [
                ['body' => 'What is the capital of France?', 'tags' => ['Tag 1', 'Tag 2']],
                ['body' => 'What is the capital of Germany?', 'tags' => ['Tag 2', 'Tag 3']],
            ],
        ];

        $response = $this->postJson(self::url, $body)
            ->assertCreated()
            ->assertJsonStructure(['data' => DeckResource::jsonStructure()])
            ->assertJson([
                'data' => [
                    'name' => $body['name'],
                    'questions' => [
                        [
                            'body' => $body['questions'][0]['body'],
                            'answers' => [],
                            'tags' => collect($body['questions'][0]['tags'])
                                ->map(fn ($name) => compact('name'))
                                ->toArray(),
                        ],
                        [
                            'body' => $body['questions'][1]['body'],
                            'answers' => [],
                            'tags' => collect($body['questions'][1]['tags'])
                                ->map(fn ($name) => compact('name'))
                                ->toArray(),
                        ],
                    ],
                ]
            ]);

        $this->assertDatabaseHasOne(Deck::class, Arr::except($body, ['questions']));

        $this->assertDatabaseHasMany(
            Question::class,
            collect($body['questions'])->map(fn ($question) => Arr::only($question, ['body']))->toArray()
        );

        $this->assertDatabaseHasMany(
            TagBind::class,
            [
                [
                    'tag_id' => Tag::where('name', $body['questions'][0]['tags'][0])->value('id'),
                    'binded_type' => Question::class,
                    'binded_id' => $response->json('data.questions.0.id'),
                ],
                [
                    'tag_id' => Tag::where('name', $body['questions'][0]['tags'][1])->value('id'),
                    'binded_type' => Question::class,
                    'binded_id' => $response->json('data.questions.0.id'),
                ],
                [
                    'tag_id' => Tag::where('name', $body['questions'][1]['tags'][0])->value('id'),
                    'binded_type' => Question::class,
                    'binded_id' => $response->json('data.questions.1.id'),
                ],
                [
                    'tag_id' => Tag::where('name', $body['questions'][1]['tags'][1])->value('id'),
                    'binded_type' => Question::class,
                    'binded_id' => $response->json('data.questions.1.id'),
                ],
            ]
        );
    }
}