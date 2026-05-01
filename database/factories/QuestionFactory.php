<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body' => fake()->sentence(),
            'quiz_id' => Quiz::factory(),
        ];
    }
    
    public function withAnswer(): static
    {
        return $this->afterCreating(function (Question $question) {
            Answer::factory()->for($question)->create();
        });
    }
}
