<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Recipe;

class IngredientService
{
    /**
     * Create an ingredient
     *
     * @param Recipe $recipe
     * @param string $name
     * @param float $amount
     * @param string $unit
     * @return Ingredient
     */
    public function createIngredient(Recipe $recipe, string $name, float $amount, string $unit): Ingredient
    {
        /** @var int $order */
        $order = $recipe->ingredients()->max('order') ?? 0;
        return $recipe->ingredients()->create([
            'name' => $name,
            'amount' => $amount,
            'unit' => $unit,
            'order' => $order + 1
        ]);
    }

    /**
     * Update an ingredient
     *
     * @param Ingredient $ingredient
     * @param string $name
     * @param float $amount
     * @param string $unit
     * @return Ingredient
     */
    public function updateIngredient(Ingredient $ingredient, string $name, float $amount, string $unit): Ingredient
    {
        $ingredient->update([
            'name' => $name,
            'amount' => $amount,
            'unit' => $unit,
        ]);

        return $ingredient;
    }

    /**
     * Update an ingredient order
     *
     * @param Ingredient $ingredient
     * @param int $order
     * @return void
     */
    public function updateIngredientOrder(Ingredient $ingredient, int $order): void
    {
        $ingredient->update([
            'order' => $order,
        ]);
    }

    /**
     * Delete an ingredient
     *
     * @param Ingredient $ingredient
     * @return void
     */
    public function deleteIngredient(Ingredient $ingredient): void
    {
        $ingredient->delete();
    }
}
