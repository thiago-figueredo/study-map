<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class QuizService
{
    public function create(array $data): Quiz
    {
        $quiz = Quiz::create(Arr::only($data, Quiz::make()->getFillable()));

        $this->syncTags($quiz, $data['tags'] ?? []);

        $questions_payload = $data['questions'] ?? [];

        $questions = $this->createQuestions($quiz, $questions_payload);
        $answers_to_create = $this->answersToCreate($questions, $questions_payload);

        Answer::query()->upsert($answers_to_create, ['body', 'question_id'], ['is_correct']);

        return $quiz->load(['questions' => ['answers', 'tags'], 'tags']);
    }

    private function syncTags(Quiz|Question $model, array $tags_data): void
    {
        $tags = collect($tags_data)
            ->map(fn (string $name) => Tag::firstOrCreate(['name' => $name])->id)
            ->all();

        $model->tags()->sync($tags);
    }

    private function createQuestions(Quiz $quiz, array $questions_data): Collection
    {
        $questions_to_create = collect($questions_data)
            ->map(fn (array $question) => Arr::except($question, ['answers', 'tags']))
            ->all();

        return $quiz->questions()->createMany($questions_to_create);
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
