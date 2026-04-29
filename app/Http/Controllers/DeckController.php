<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDeckRequest;
use App\Http\Resources\DeckResource;
use App\Models\Deck;
use App\Services\DeckService;
use Illuminate\Http\Resources\Json\JsonResource;

class DeckController extends Controller
{
    public function __construct(private DeckService $deck_service) {}

    public function index(): JsonResource
    {
        return DeckResource::collection(Deck::all());
    }

    public function store(CreateDeckRequest $request): JsonResource
    {
        return DeckResource::make($this->deck_service->create($request->validated()));
    }
}
