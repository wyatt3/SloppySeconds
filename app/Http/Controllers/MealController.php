<?php

namespace App\Http\Controllers;

class MealController extends Controller
{
    /**
     * Show the meal planner
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Meals/Index');
    }
}
