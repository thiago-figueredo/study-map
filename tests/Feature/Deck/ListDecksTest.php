<?php

namespace Tests\Feature\Deck;

use App\Http\Resources\DeckResource;
use App\Models\Deck;
use Tests\TestCase;

class ListDecksTest extends TestCase
{
    const url = '/api/decks';

    public function test_list_decks(): void
    {
        $decks = Deck::factory(3)->create();

        $this->getJson(self::url)
            ->assertOk()
            ->assertJsonCount($decks->count(), 'data')
            ->assertJson(['data' => DeckResource::collection($decks)->resolve()]);
    }
}
