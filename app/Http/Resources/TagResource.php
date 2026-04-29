<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TagResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->formatToArray([
            'name' => $this->name,
        ], excluded: ['created_at', 'updated_at', 'deleted_at']);
    }

    public static function jsonStructure(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
