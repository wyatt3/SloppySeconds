<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShoppingListRequest;
use App\Models\User;
use App\Services\ShoppingListService;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class ShoppingListController extends Controller
{
    public function __construct(private ShoppingListService $shoppingListService) {}

    /**
     * Generate a shopping list for the given date range
     *
     * @param ShoppingListRequest $request
     * @return Response
     */
    public function index(ShoppingListRequest $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $shoppingList = $this->shoppingListService->generateShoppingList(
            $startDate,
            $endDate,
            $user->userGroup
        );

        // Transform collection to array with computed id for frontend use
        return response(
            $shoppingList->values()->map(fn ($ingredient, $index) => [
                'id' => $index,
                'name' => $ingredient->name,
                'amount' => $ingredient->amount,
                'unit' => $ingredient->unit,
            ])
        );
    }
}
