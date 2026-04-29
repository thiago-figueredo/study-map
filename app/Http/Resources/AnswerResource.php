<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AnswerResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->formatToArray([
            'body' => $this->body,
            'is_correct' => $this->is_correct,
            'question_id' => $this->question_id,
        ]);
    }
}
