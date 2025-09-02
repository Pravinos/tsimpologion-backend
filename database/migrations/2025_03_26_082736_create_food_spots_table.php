<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('food_spots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('city');
            $table->string('address');
            $table->text('description')->nullable();
            $table->string('info_link')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->json('images')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->string('phone')->nullable();
            $table->json('business_hours')->nullable();
            $table->json('social_links')->nullable();
            $table->string('price_range', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_spots');
    }
};
