<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDeckRequest;
use App\Http\Resources\DeckResource;
use App\Models\Answer;
use App\Models\Deck;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class DeckController extends Controller
{
    public function index(): JsonResource
    {
        return DeckResource::collection(Deck::all());
    }

    public function store(CreateDeckRequest $request): JsonResource
    {
        $deck = Deck::create(Arr::only($request->validated(), Deck::make()->getFillable()));
        $questions_to_create = [];
        $answers_to_create = [];

        collect($request->validated('questions') ?? [])->each(function ($question) use (&$questions_to_create) {
            $questions_to_create[] = Arr::except($question, 'answers');
        });

        $questions = $deck->questions()->createMany($questions_to_create);

        collect($questions)->each(function ($question) use (&$answers_to_create, $request) {
            $question_created = collect($request->validated('questions'))->firstWhere('body', $question['body']);
            $answers_to_create[] = collect($question_created['answers'] ?? [])
                ->map(fn ($answer) => [
                    ...$answer,
                    'question_id' => $question['id'],
                ])
                ->toArray();
        });

        Answer::query()->upsert(array_merge(...$answers_to_create), ['body', 'question_id'], ['is_correct']);

        return DeckResource::make($deck->load('questions.answers'));
    }
}
