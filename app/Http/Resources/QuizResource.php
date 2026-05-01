<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class QuizResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return $this->formatToArray([
            'name' => $this->name,
            'questions' => $this->whenLoaded(
                'questions',
                QuestionResource::collection($this->questions)->resolve()
            ),
            'tags' => $this->whenLoaded(
                'tags',
                TagResource::collection($this->tags)->resolve()
            ),
        ]);
    }

    public static function jsonStructure(): array
    {
        return [
            'name',
            'questions' => [
                '*' => QuestionResource::jsonStructure(),
            ],
            ...parent::jsonStructure(),
        ];
    }

}
