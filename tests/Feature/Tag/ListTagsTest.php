<?php

namespace Tests\Feature\Tag;

use App\Models\Tag;
use Tests\TestCase;

class ListTagsTest extends TestCase
{
    private const url = '/api/tags';

    public function test_list_tags(): void
    {
        $tags = Tag::factory(2)->create();

        $this->getJson(self::url)
            ->assertOk()
            ->assertJsonCount($tags->count(), 'data')
            ->assertJson([
                'data' => collect($tags)->map(fn ($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ])->toArray(),
            ]);
    }
}