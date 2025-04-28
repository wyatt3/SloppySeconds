<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\UserGroup;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RecipeService
{
    /**
     * Create a recipe
     *
     * @param string $name
     * @param string $description
     * @param ?UploadedFile $image
     * @param UserGroup $userGroup
     * @return Recipe
     */
    public function create(string $name, string $description, ?UploadedFile $image, UserGroup $userGroup): Recipe
    {
        $recipe = Recipe::create([
            'name' => $name,
            'description' => $description,
            'image' => null,
            'user_group_id' => $userGroup->id,
        ]);

        if ($image) {
            /** @var string $id */
            $id = $recipe->getKey();
            /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
            $disk = Storage::disk('recipeImages');
            $disk->putFileAs('', $image, $id . $image->getClientOriginalExtension());
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
    public function update(Recipe $recipe, ?string $name, ?string $description, ?UploadedFile $image): Recipe
    {
        $update = [
            'name' => $name,
            'description' => $description,
        ];
        //filter null values
        $update = array_filter($update);

        if ($image) {
            /** @var string $id */
            $id = $recipe->getKey();
            /** @var string $fileName */
            $fileName = $id . $image->getClientOriginalExtension();
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
    public function delete(Recipe $recipe): void
    {
        $recipe->delete();
        $recipe->directions()->delete();
        if ($recipe->image) {
            Storage::disk('recipeImages')->delete($recipe->image);
        }
    }
}
