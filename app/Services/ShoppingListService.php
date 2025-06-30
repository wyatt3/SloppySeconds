<?php

namespace App\Services;

use App\Models\Meal;
use App\Models\UserGroup;
use Illuminate\Support\Carbon;

class ShoppingListService
{
    public function generateShoppingList(Carbon $startDate, Carbon $endDate, UserGroup $userGroup) {}

    public function generateShoppingListForSingleMeal(Meal $meal) {}
}
