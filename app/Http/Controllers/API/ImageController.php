<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Services\ImageService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function getRecipeImage(Recipe $recipe): BinaryFileResponse
    {
        return response()->file($this->imageService->getRecipeImage($recipe));
    }
}
