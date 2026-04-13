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
        Schema::create('meal_recipes', function (Blueprint $table) {
            $table->integer('meal_id');
            $table->integer('recipe_id');
            $table->unique(['meal_id', 'recipe_id'], 'meal_recipes_meal_recipe_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_recipes');
    }
};
