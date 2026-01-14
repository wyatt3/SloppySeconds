<?php

namespace App\Http\Controllers;

class ShoppingListController extends Controller
{
    /**
     * Show the shopping list page
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('ShoppingList/Index');
    }
}
