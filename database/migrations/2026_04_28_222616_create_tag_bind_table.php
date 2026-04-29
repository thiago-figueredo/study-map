<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const table = 'tag_bind';

    public function up(): void
    {
        Schema::create(self::table, function (Blueprint $table) {
            $table->id();

            $table->foreignId('tag_id')->constrained('tags');
            $table->morphs('binded');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::table);
    }
};
