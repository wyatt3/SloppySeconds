<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MealIndexRequest;
use App\Http\Requests\MealRequest;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\User;
use App\Services\MealService;
use Illuminate\Http\Response;

class MealController extends Controller
{
    public function __construct(private MealService $mealService) {}

    /**
     * Display a listing of the resource.
     *
     * @param MealIndexRequest $request
     * @return Response
     */
    public function index(MealIndexRequest $request): Response
    {
        /** @var \Illuminate\Support\Carbon $startDate */
        $startDate = $request->input('start_date');
        /** @var \Illuminate\Support\Carbon $endDate */
        $endDate = $request->input('end_date');
        /** @var User $user */
        $user = $request->user();

        return response(
            $user
                ->meals()
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date')
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MealRequest $request
     * @return Response
     */
    public function store(MealRequest $request): Response
    {
        /** @var \Illuminate\Support\Carbon $date */
        $date = $request->input('date');
        /** @var User $user */
        $user = $request->user();

        $meal = $this->mealService->createMeal($date, $user->userGroup);

        return response($meal, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Meal $meal
     * @return Response
     */
    public function destroy(Meal $meal): Response
    {
        $this->mealService->deleteMeal($meal);

        return response()->noContent();
    }

    /**
     * Add a recipe to a meal
     *
     * @param Meal $meal
     * @param Recipe $recipe
     * @return Response
     */
    public function addRecipe(Meal $meal, Recipe $recipe): Response
    {
        $this->mealService->addRecipe($meal, $recipe);

        return response($meal->load('recipes'));
    }

    /**
     * Remove a recipe from a meal
     *
     * @param Meal $meal
     * @param Recipe $recipe
     * @return Response
     */
    public function removeRecipe(Meal $meal, Recipe $recipe): Response
    {
        $this->mealService->removeRecipe($meal, $recipe);

        return response($meal->load('recipes'));
    }
}
