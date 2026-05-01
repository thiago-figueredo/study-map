<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Services\ReviewService;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    public function __invoke(ReviewRequest $request, ReviewService $review_service): Response
    {
        $review_service->review($request);

        return response()->noContent();
    }
}