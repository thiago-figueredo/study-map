<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Deck;
use App\Models\Question;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class DeckService
{
    public function create(array $data): Deck
    {
        $deck = Deck::create(Arr::only($data, Deck::make()->getFillable()));

        $this->syncTags($deck, $data['tags'] ?? []);

        $questions_payload = $data['questions'] ?? [];

        $questions = $this->createQuestions($deck, $questions_payload);
        $answers_to_create = $this->answersToCreate($questions, $questions_payload);

        Answer::query()->upsert($answers_to_create, ['body', 'question_id'], ['is_correct']);

        return $deck->load(['questions' => ['answers', 'tags'], 'tags']);
    }

    private function syncTags(Deck|Question $model, array $tags_data): void
    {
        $tags = collect($tags_data)
            ->map(fn (string $name) => Tag::firstOrCreate(['name' => $name])->id)
            ->all();

        $model->tags()->sync($tags);
    }

    private function createQuestions(Deck $deck, array $questions_data): Collection
    {
        $questions_to_create = collect($questions_data)
            ->map(fn (array $question) => Arr::except($question, ['answers', 'tags']))
            ->all();

        return $deck->questions()->createMany($questions_to_create);
    }

    private function answersToCreate(Collection $questions, array $questions_data): array
    {
        return $questions->reduce(function ($result, $question) use ($questions_data) {
            if (!isset($question['answers'])) {
                return $result;
            }

            $question_input = collect($questions_data)->firstWhere('body', $question->body);

            $this->syncTags($question, $question_input['tags'] ?? []);

            $answers_to_create = collect($question_input['answers'] ?? [])
                ->map(fn (array $answer) => [...$answer, 'question_id' => $question->id])
                ->all();

            return array_merge($result, $answers_to_create);
        }, []);
    }
}
