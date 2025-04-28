<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Direction;
use App\Models\Recipe;
use App\Services\ImageService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function getRecipeImage(Recipe $recipe): BinaryFileResponse
    {
        return response()->file($this->imageService->getRecipeImage($recipe));
    }

    public function getDirectionImage(Direction $direction): BinaryFileResponse
    {
        /** @var ?string $file */
        $file = $this->imageService->getDirectionImage($direction);
        if (!$file) {
            return abort(Response::HTTP_NOT_FOUND);
        }
        return response()->file($file);
    }
}
