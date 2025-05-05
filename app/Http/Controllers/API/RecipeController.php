<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
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

    public function store(CreateRecipeRequest $request): Response
    {
        /** @var string $name */
        $name = $request->input('name');
        /** @var string $description */
        $description = $request->input('description');
        /** @var ?\Illuminate\Http\UploadedFile $image */
        $image = $request->file('image');
        /** @var \App\Models\UserGroup $userGroup */
        $userGroup = Auth::user()->userGroup;
        $recipe = $this->recipeService->createRecipe($name, $description, $image, $userGroup);
        return response($recipe, 201);
    }

    public function update(Recipe $recipe, UpdateRecipeRequest $request)
    {
        /** @var ?string $name */
        $name = $request->input('name');
        /** @var ?string $description */
        $description = $request->input('description');
        /** @var ?\Illuminate\Http\UploadedFile $image */
        $image = $request->file('image');
        $recipe = $this->recipeService->updateRecipe($recipe, $name, $description, $image);
        return response($recipe);
    }

    public function delete(Recipe $recipe): Response
    {
        $this->recipeService->deleteRecipe($recipe);
        return response()->noContent();
    }
}
