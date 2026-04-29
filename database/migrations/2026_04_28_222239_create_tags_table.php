<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const table = 'tags';

    public function up(): void
    {
        Schema::create(self::table, function (Blueprint $table) {
            $table->id();

            $table->string('name', 50);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::table);
    }
};
