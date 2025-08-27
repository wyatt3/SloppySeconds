<?php

namespace App\Http\Controllers;

use App\Models\Recipe;

class RecipeController extends Controller
{
    /**
     * Show all recipes
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Recipes/Index');
    }

    /**
     * Show a single recipe
     *
     * @param Recipe $recipe
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function show(Recipe $recipe): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Recipes/Show', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Create a new recipe
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function create(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Recipes/Create');
    }
}
