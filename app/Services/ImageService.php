<?php

namespace App\Services;

use App\Models\Direction;
use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Get the image of a recipe
     *
     * @param Recipe $recipe
     * @return string
     */
    public function getRecipeImage(Recipe $recipe): string
    {
        /** @var string $default */
        $default = Storage::disk('recipeImages')->path('default.jpg');

        return  $recipe->image ? Storage::disk('recipeImages')->path($recipe->image) : $default;
    }

    /**
     * Get the image of a direction
     *
     * @param Direction $direction
     * @return ?string
     */
    public function getDirectionImage(Direction $direction): ?string
    {
        if (!$direction->image) {
            return null;
        }
        return Storage::disk('recipeImages')->path($direction->image);
    }
}
