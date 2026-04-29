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
        ]);
    }

    public static function jsonStructure(): array
    {
        return [
            'body',
            ...parent::jsonStructure(),
        ];
    }
}

