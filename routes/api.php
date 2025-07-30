<?php

use App\Http\Middleware\EnsureUserOwnsMeal;
use App\Http\Middleware\EnsureUserOwnsRecipe;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('api.login');

    Route::prefix('meals')->group(function () {
        Route::get('/', [App\Http\Controllers\API\MealController::class, 'index'])->name('api.meals.index');
        Route::post('/', [App\Http\Controllers\API\MealController::class, 'store'])->name('api.meals.store');
        Route::prefix('{meal}')->middleware(EnsureUserOwnsMeal::class)->group(function () {
            Route::put('/', [App\Http\Controllers\API\MealController::class, 'update'])->name('api.meals.update');
            Route::get('/', [App\Http\Controllers\API\MealController::class, 'show'])->name('api.meals.show');
            Route::delete('/', [App\Http\Controllers\API\MealController::class, 'destroy'])->name('api.meals.destroy');
            Route::prefix('/recipes/{recipe}')->middleware(EnsureUserOwnsRecipe::class)->group(function () {
                Route::post('/', [App\Http\Controllers\API\MealController::class, 'addRecipe'])->name('api.meals.add-recipe');
                Route::delete('/', [App\Http\Controllers\API\MealController::class, 'removeRecipe'])->name('api.meals.remove-recipe');
            });
        });
    });

    Route::prefix('recipes')->group(function () {
        Route::get('/', [App\Http\Controllers\API\RecipeController::class, 'index'])->name('api.recipes.index');
        Route::post('/', [App\Http\Controllers\API\RecipeController::class, 'store'])->name('api.recipes.store');

        Route::prefix('{recipe}')->middleware(EnsureUserOwnsRecipe::class)->group(function () {
            Route::get('/', [App\Http\Controllers\API\RecipeController::class, 'show'])->name('api.recipes.show');
            Route::put('/', [App\Http\Controllers\API\RecipeController::class, 'update'])->name('api.recipes.update');
            Route::delete('/', [App\Http\Controllers\API\RecipeController::class, 'delete'])->name('api.recipes.delete');

            Route::prefix('/directions')->group(function () {
                Route::put('/orders', [App\Http\Controllers\API\DirectionController::class, 'updateOrders'])->name('api.directions.update-orders');
                Route::post('/', [App\Http\Controllers\API\DirectionController::class, 'store'])->name('api.directions.store');
                Route::put('/{direction}', [App\Http\Controllers\API\DirectionController::class, 'update'])->name('api.directions.update');
                Route::delete('/{direction}', [App\Http\Controllers\API\DirectionController::class, 'delete'])->name('api.directions.delete');
            });

            Route::prefix('/ingredients')->group(function () {
                Route::put('/orders', [App\Http\Controllers\API\IngredientController::class, 'updateOrders'])->name('api.ingredients.update-orders');
                Route::post('/', [App\Http\Controllers\API\IngredientController::class, 'store'])->name('api.ingredients.store');
                Route::put('/{ingredient}', [App\Http\Controllers\API\IngredientController::class, 'update'])->name('api.ingredients.update');
                Route::delete('/{ingredient}', [App\Http\Controllers\API\IngredientController::class, 'delete'])->name('api.ingredients.delete');
            });
        });
    });
});
