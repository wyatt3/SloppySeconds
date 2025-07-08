<?php

namespace App\Services;

use App\Enums\RecipeType;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\UserGroup;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RecipeService
{
    public function __construct(
        private DirectionService $directionService,
        private IngredientService $ingredientService,
        private MealService $mealService,
    ) {}

    /**
     * Create a recipe
     *
     * @param string $name
     * @param string $description
     * @param ?UploadedFile $image
     * @param UserGroup $userGroup
     * @return Recipe
     */
    public function createRecipe(string $name, string $description, ?UploadedFile $image, UserGroup $userGroup, RecipeType $type): Recipe
    {
        $recipe = Recipe::create([
            'name' => $name,
            'description' => $description,
            'image' => null,
            'user_group_id' => $userGroup->getKey(),
            'type' => $type->name
        ]);

        if ($image) {
            /** @var string $id */
            $id = $recipe->getKey();
            /** @var string $fileName */
            $fileName = "{$id}.{$image->getClientOriginalExtension()}";
            /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
            $disk = Storage::disk('recipeImages');
            $disk->putFileAs('', $image, $fileName);
            $recipe->update(['image' => $fileName]);
        }

        return $recipe;
    }

    /**
     * Update a recipe
     *
     * @param Recipe $recipe
     * @param ?string $name
     * @param ?string $description
     * @param ?UploadedFile $image
     * @return Recipe
     */
    public function updateRecipe(Recipe $recipe, ?string $name, ?string $description, ?UploadedFile $image, RecipeType $type): Recipe
    {
        $update = [
            'name' => $name,
            'description' => $description,
            'type' => $type->name
        ];
        //filter null values
        $update = array_filter($update);

        if ($image) {
            /** @var string $id */
            $id = $recipe->getKey();
            /** @var string $fileName */
            $fileName = "{$id}.{$image->getClientOriginalExtension()}";
            /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
            $disk = Storage::disk('recipeImages');
            $disk->putFileAs('', $image, $fileName);
            $update['image'] = $fileName;
        }

        $recipe->update($update);

        return $recipe;
    }

    /**
     * Delete a recipe
     *
     * @param Recipe $recipe
     * @return void
     */
    public function deleteRecipe(Recipe $recipe): void
    {
        $recipe->delete();
        $recipe->meals()->each(fn(Meal $meal) => $this->mealService->removeRecipe($meal, $recipe));
        $recipe->directions()->each(fn($direction) => $this->directionService->deleteDirection($direction));
        $recipe->ingredients()->each(fn($ingredient) => $this->ingredientService->deleteIngredient($ingredient));
        if ($recipe->image) {
            Storage::disk('recipeImages')->delete($recipe->image);
        }
    }
}
