<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DirectionRequest;
use App\Http\Requests\UpdateDirectionOrdersRequest;
use App\Models\Direction;
use App\Models\Recipe;
use App\Services\DirectionService;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;

class DirectionController extends Controller
{
    public function __construct(private DirectionService $directionService) {}

    /**
     * Store a direction
     *
     * @param DirectionRequest $request
     * @param Recipe $recipe
     * @return Response
     */
    public function store(DirectionRequest $request, Recipe $recipe): Response
    {
        /** @var ?string $title */
        $title = $request->input('title');
        /** @var string $content */
        $content = $request->input('content');
        /** @var ?UploadedFile $image */
        $image = $request->file('image');
        $direction = $this->directionService->createDirection($title, $content, $image, $recipe);
        return response($direction, 201);
    }

    /**
     * Update a direction
     *
     * @param DirectionRequest $request
     * @param Recipe $recipe
     * @param Direction $direction
     * @return Response
     */
    public function update(DirectionRequest $request, Recipe $recipe, Direction $direction): Response
    {
        /** @var ?string $title */
        $title = $request->input('title');
        /** @var string $content */
        $content = $request->input('content');
        /** @var ?UploadedFile $image */
        $image = $request->file('image');
        $direction = $this->directionService->updateDirection($direction, $title, $content, $image);
        return response($direction);
    }

    /**
     * Update directions order
     *
     * @param UpdateDirectionOrdersRequest $request
     * @param Recipe $recipe
     * @return Response
     */
    public function updateOrders(UpdateDirectionOrdersRequest $request, Recipe $recipe): Response
    {
        /** @var array<array<string, int>> $directions */
        $directions = $request->input('directions');
        foreach ($directions as $direction) {
            /** @var int $id */
            $id = $direction['id'];
            /** @var Direction $direction */
            $direction = Direction::findOrFail($id);
            /** @var int $order */
            $order = $direction['order'];
            $this->directionService->updateDirectionOrder($direction, $order);
        }
        return response()->noContent();
    }

    /**
     * Delete a direction
     *
     * @param Direction $direction
     * @return Response
     */
    public function delete(Recipe $recipe, Direction $direction): Response
    {
        $this->directionService->deleteDirection($direction);
        return response()->noContent();
    }
}
