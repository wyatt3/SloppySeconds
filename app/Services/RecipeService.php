<?php

namespace App\Services;

use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;

class RecipeService
{
    public function create(array $data): Recipe {}

    public function update(Recipe $recipe, array $data): Recipe {}

    public function delete(Recipe $recipe): void
    {
        $recipe->delete();
        $recipe->directions()->delete();
        Storage::disk('recipeImages')->delete($recipe->image);
    }
}
