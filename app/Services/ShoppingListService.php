<?php

namespace App\Services;

use App\Models\UserGroup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ShoppingListService
{
    /**
     * Generate a shopping list
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param UserGroup $userGroup
     * @return Collection<int, \App\Models\Ingredient>
     */
    public function generateShoppingList(Carbon $startDate, Carbon $endDate, UserGroup $userGroup): Collection
    {
        $meals = $userGroup->meals()->whereBetween('date', [$startDate, $endDate])->get();
        /** @var Collection<int, \App\Models\Ingredient> $shoppingList */
        $shoppingList = collect();
        foreach ($meals as $meal) {
            /** @var Collection<int, \App\Models\Ingredient> $ingredients */
            $ingredients = $meal->recipes()->with('ingredients')->get()->flatMap(function ($recipe) {
                return $recipe->ingredients;
            });
            $shoppingList = $this->combineLikeIngredients($shoppingList, $ingredients);
        }

        return $shoppingList;
    }

    /**
     * Combine like ingredients
     *
     * @param Collection<int, \App\Models\Ingredient> $list
     * @param Collection<int, \App\Models\Ingredient> $ingredients
     * @return Collection<int, \App\Models\Ingredient>
     */
    public function combineLikeIngredients(Collection $list, Collection $ingredients): Collection
    {
        foreach ($ingredients as $ingredient) {
            $exists = $list->where('name', $ingredient->name)->first();
            if ($exists && $exists->unit === $ingredient->unit) {
                $list->where('name', $ingredient->name)->first()->increment('amount', $ingredient->amount);
            } else {
                $list->push($ingredient);
            }
        }

        return $list->sortBy('name');
    }
}
