<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizController extends Controller
{
    public function __construct(private QuizService $quiz_service) {}

    public function index(): JsonResource
    {
        return QuizResource::collection(Quiz::all());
    }

    public function store(CreateQuizRequest $request): JsonResource
    {
        return QuizResource::make($this->quiz_service->create($request->validated()));
    }
}
