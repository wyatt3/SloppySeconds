<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Direction;
use App\Services\DirectionService;
use Illuminate\Http\Response;

class DirectionController extends Controller
{
    public function __construct(private DirectionService $directionService) {}

    public function storeDirection() {}

    public function updateDirection() {}

    public function updateDirectionOrders() {}

    public function deleteDirection(Direction $direction): Response
    {
        $this->directionService->deleteDirection($direction);
        return response()->noContent();
    }
}
