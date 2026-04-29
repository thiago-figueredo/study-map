<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const table = 'answers';

    public function up(): void
    {
        Schema::create(self::table, function (Blueprint $table) {
            $table->id();

            $table->string('body', 255);
            $table->boolean('is_correct');
            $table->foreignId('question_id')->constrained('questions');

            $table->unique(['body', 'question_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::table);
    }
};
