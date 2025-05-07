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
            $table->enum('category', ['Restaurant', 'Taverna', 'Mezedopoleion', 'Brunch', 'Pizza', 'Sushi', 'Burgeradiko', 'Tsipouradiko']);
            $table->string('city');
            $table->string('address');
            $table->text('description');
            $table->string('info_link');
            $table->float('rating')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->json('images')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
