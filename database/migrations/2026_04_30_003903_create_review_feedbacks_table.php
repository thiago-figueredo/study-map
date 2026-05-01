<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ReviewFeedback as ReviewFeedbackEnum;

return new class extends Migration
{
    const table = 'review_feedbacks';

    public function up(): void
    {
        Schema::create(self::table, function (Blueprint $table) {
            $table->id();

            $table->foreignId('question_id')->constrained('questions');
            $table->tinyInteger('feedback');
            $table->decimal('easy_factor', 8, 2)->default(2.5);
            $table->unsignedInteger('interval')->default(0);
            $table->unsignedInteger('repetitions')->default(0);
            $table->date('next_review_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::table);
    }
};
