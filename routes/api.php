<?php

use App\Http\Middleware\EnsureUserOwnsRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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
