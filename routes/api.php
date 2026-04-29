<?php

use App\Http\Controllers\DeckController;
use Illuminate\Support\Facades\Route;

Route::get('/decks', [DeckController::class, 'index']);
Route::post('/decks', [DeckController::class, 'store']);