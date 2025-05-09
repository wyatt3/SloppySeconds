<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IngredientRequest;
use App\Http\Requests\UpdateIngredientOrdersRequest;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Services\IngredientService;
use Illuminate\Http\Response;

class IngredientController extends Controller
{
    public function __construct(private IngredientService $ingredientService) {}

    /**
     * Store an ingredient
     *
     * @param IngredientRequest $request
     * @param Recipe $recipe
     * @return Response
     */
    public function store(IngredientRequest $request, Recipe $recipe): Response
    {
        /** @var string $name */
        $name = $request->input('name');
        /** @var float $amount */
        $amount = $request->input('amount');
        /** @var string $unit */
        $unit = $request->input('unit');
        $ingredient = $this->ingredientService->createIngredient($recipe, $name, $amount, $unit);
        return response($ingredient, 201);
    }

    /**
     * Update an ingredient
     *
     * @param IngredientRequest $request
     * @param Recipe $recipe
     * @param Ingredient $ingredient
     * @return Response
     */
    public function update(IngredientRequest $request, Recipe $recipe, Ingredient $ingredient): Response
    {
        /** @var string $name */
        $name = $request->input('name');
        /** @var float $amount */
        $amount = $request->input('amount');
        /** @var string $unit */
        $unit = $request->input('unit');
        $ingredient = $this->ingredientService->updateIngredient($ingredient, $name, $amount, $unit);
        return response($ingredient);
    }

    public function updateOrders(UpdateIngredientOrdersRequest $request, Recipe $recipe): Response
    {
        /** @var array<array<string, int>> $ingredients */
        $ingredients = $request->input('ingredients');
        foreach ($ingredients as $ingredient) {
            /** @var int $id */
            $id = $ingredient['id'];
            /** @var Ingredient $ingredient */
            $ingredient = Ingredient::findOrFail($id);
            /** @var int $order */
            $order = $ingredient['order'];
            $this->ingredientService->updateIngredientOrder($ingredient, $order);
        }
        return response()->noContent();
    }

    /**
     * Delete an ingredient
     *
     * @param Recipe $recipe
     * @param Ingredient $ingredient
     * @return Response
     */
    public function delete(Recipe $recipe, Ingredient $ingredient): Response
    {
        $this->ingredientService->deleteIngredient($ingredient);
        return response()->noContent();
    }
}
