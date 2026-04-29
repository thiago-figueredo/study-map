<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class QuestionResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return $this->formatToArray([
            'body' => $this->body,
            'answers' => $this->whenLoaded('answers', AnswerResource::collection($this->answers)),
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)->resolve()),
        ]);
    }

    public static function jsonStructure(): array
    {
        return [
            'body',
            'answers' => ['*' => AnswerResource::jsonStructure()],
            'tags' => ['*' => TagResource::jsonStructure()],
            ...parent::jsonStructure(),
        ];
    }
}

