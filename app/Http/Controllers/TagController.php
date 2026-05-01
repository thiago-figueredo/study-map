<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class TagController extends Controller
{
    public function index(): JsonResource
    {
        return TagResource::collection(Tag::all());
    }
}