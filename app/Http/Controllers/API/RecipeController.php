<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    /**
     * Get all recipes that the user has access to
     *
     * @return Response
     */
    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return response($user->recipes()->get());
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
}
