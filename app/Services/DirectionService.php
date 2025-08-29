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
     * @param ?string $title
     * @param string $content
     * @param ?UploadedFile $image
     * @param Recipe $recipe
     * @return Direction
     */
    public function createDirection(?string $title, string $content, ?UploadedFile $image, Recipe $recipe): Direction
    {
        /** @var int $order */
        $order = $recipe->directions()->max('order') ?? 0;
        $direction = $recipe->directions()->create([
            'title' => $title,
            'content' => $content,
            'image' => null,
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
     * @param ?string $title
     * @param string $content
     * @param ?UploadedFile $image
     * @return Direction
     */
    public function updateDirection(Direction $direction, ?string $title, string $content, ?UploadedFile $image): Direction
    {
        $update = [
            'title' => $title,
            'content' => $content,
        ];

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
     * @return void
     */
    public function updateDirectionOrder(Direction $direction, int $order): void
    {
        $direction->update(['order' => $order]);
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
