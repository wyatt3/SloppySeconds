<?php

namespace App\Services;

use App\Models\Direction;
use App\Models\Recipe;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DirectionService
{

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
            $fileName = "{$id}.{$image->getClientOriginalExtension()}";
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
            $fileName = "{$id}.{$image->getClientOriginalExtension()}";
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
