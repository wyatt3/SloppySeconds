<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecipeRequest;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{

    public function __construct(private RecipeService $recipeService) {}
    /**
     * Get all recipes that the user has access to
     *
     * @return Response
     */
    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return response($user->recipes()->orderBy('name')->get());
    }

    /**
     * Get a single recipe
     *
     * @param Recipe $recipe
     * @return Response
     */
    public function show(Recipe $recipe): Response
    {
        return response($recipe);
    }

    /**
     * Create a recipe
     *
     * @param RecipeRequest $request
     * @return Response
     */
    public function store(RecipeRequest $request): Response
    {
        /** @var string $name */
        $name = $request->input('name');
        /** @var string $description */
        $description = $request->input('description');
        /** @var ?\Illuminate\Http\UploadedFile $image */
        $image = $request->file('image');
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var \App\Models\UserGroup $userGroup */
        $userGroup = $user->userGroup;
        $recipe = $this->recipeService->createRecipe($name, $description, $image, $userGroup);
        return response($recipe, 201);
    }

    /**
     * Update a recipe
     *
     * @param Recipe $recipe
     * @param RecipeRequest $request
     * @return Response
     */
    public function update(Recipe $recipe, RecipeRequest $request): Response
    {
        /** @var string $name */
        $name = $request->input('name');
        /** @var string $description */
        $description = $request->input('description');
        /** @var ?\Illuminate\Http\UploadedFile $image */
        $image = $request->file('image');
        $recipe = $this->recipeService->updateRecipe($recipe, $name, $description, $image);
        return response($recipe);
    }

    /**
     * Delete a recipe
     *
     * @param Recipe $recipe
     * @return Response
     */
    public function delete(Recipe $recipe): Response
    {
        $this->recipeService->deleteRecipe($recipe);
        return response()->noContent();
    }
}
