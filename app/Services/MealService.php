<?php

namespace App\Services;

use App\Models\Meal;
use App\Models\Recipe;
use App\Models\UserGroup;
use Illuminate\Support\Carbon;

class MealService
{
    public function createMeal(Carbon $date, UserGroup $userGroup): Meal
    {
        return Meal::create([
            'date' => $date,
            'user_group_id' => $userGroup->id,
        ]);
    }

    public function updateMeal(Meal $meal, Carbon $date): void
    {
        $meal->update([
            'date' => $date
        ]);
    }

    /**
     * Add a recipe to a meal
     *
     * @param Meal $meal
     * @param Recipe $recipe
     * @return void
     */
    public function addRecipe(Meal $meal, Recipe $recipe): void
    {
        $meal->recipes()->attach($recipe);
    }

    /**
     * Remove a recipe from a meal
     *
     * @param Meal $meal
     * @param Recipe $recipe
     * @return void
     */
    public function removeRecipe(Meal $meal, Recipe $recipe): void
    {
        $meal->recipes()->detach($recipe);
    }

    /**
     * Delete a meal
     *
     * @param Meal $meal
     * @return void
     */
    public function deleteMeal(Meal $meal): void
    {
        $meal->delete();
    }
}
