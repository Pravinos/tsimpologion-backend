<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('favourites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_spot_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'food_spot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favourites');
    }
};
