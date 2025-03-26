<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_spot_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->integer('rating')->unsigned()->min(1)->max(5);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Ensure one review per user per food spot
            $table->unique(['user_id', 'food_spot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};