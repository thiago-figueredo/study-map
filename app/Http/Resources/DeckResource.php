<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DeckResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return $this->formatToArray([
            'name' => $this->name,
            'questions' => $this->whenLoaded(
                'questions',
                QuestionResource::collection($this->questions)->resolve()
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
