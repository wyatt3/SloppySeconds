<?php

namespace App\Services;

use App\Models\Direction;
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
    public function createRecipe(string $name, string $description, ?UploadedFile $image, UserGroup $userGroup): Recipe
    {
        $recipe = Recipe::create([
            'name' => $name,
            'description' => $description,
            'image' => null,
            'user_group_id' => $userGroup->getKey(),
        ]);

        if ($image) {
            /** @var string $id */
            $id = $recipe->getKey();
            /** @var string $fileName */
            $fileName = $id . '.' . $image->getClientOriginalExtension();
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
    public function updateRecipe(Recipe $recipe, ?string $name, ?string $description, ?UploadedFile $image): Recipe
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
            $fileName = $id . '.' . $image->getClientOriginalExtension();
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
        $recipe->directions()->each(fn($direction) => $this->deleteDirection($direction));
        if ($recipe->image) {
            Storage::disk('recipeImages')->delete($recipe->image);
        }
    }

    /**
     * Create a direction
     *
     * @param string $content
     * @param ?UploadedFile $image
     * @param Recipe $recipe
     * @return Direction
     */
    public function createDirection(string $content, ?UploadedFile $image, Recipe $recipe): Direction
    {
        /** @var int $order */
        $order = $recipe->directions()->max('order');
        $direction = Direction::create([
            'content' => $content,
            'image' => null,
            'recipe_id' => $recipe->getKey(),
            'order' =>  $order + 1,
        ]);

        if ($image) {
            /** @var string $id */
            $id = $direction->getKey();
            /** @var string $fileName */
            $fileName = $id . '.' . $image->getClientOriginalExtension();
            /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
            $disk = Storage::disk('recipeImages');
            $disk->putFileAs('directions', $image, $fileName);
            $direction->update(['image' => $fileName]);
        }

        return $direction;
    }

    /**
     * Update a direction
     *
     * @param Direction $direction
     * @param string $content
     * @param ?UploadedFile $image
     * @return Direction
     */
    public function updateDirection(Direction $direction, string $content, ?UploadedFile $image): Direction
    {
        $update = [
            'content' => $content,
        ];
        //filter null values
        $update = array_filter($update);

        if ($image) {
            /** @var string $id */
            $id = $direction->getKey();
            /** @var string $fileName */
            $fileName = $id . '.' . $image->getClientOriginalExtension();
            /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
            $disk = Storage::disk('recipeImages');
            $disk->putFileAs('directions', $image, $fileName);
            $update['image'] = $fileName;
        }

        $direction->update($update);

        return $direction;
    }

    /**
     * Update direction order
     *
     * @param Direction $direction
     * @param integer $order
     * @return Direction
     */
    public function updateDirectionOrder(Direction $direction, int $order): Direction
    {
        $direction->update(['order' => $order]);

        return $direction;
    }

    /**
     * Delete a direction
     *
     * @param Direction $direction
     * @return void
     */
    public function deleteDirection(Direction $direction): void
    {
        $direction->delete();
        if ($direction->image) {
            Storage::disk('recipeImages')->delete("directions/" . $direction->image);
        }
    }
}
