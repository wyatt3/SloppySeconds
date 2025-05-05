<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('recipes')->group(function () {
    Route::get('/', [App\Http\Controllers\API\RecipeController::class, 'index'])->name('api.recipes.index');
    Route::get('/{recipe}', [App\Http\Controllers\API\RecipeController::class, 'show'])->name('api.recipes.show');
    Route::post('/', [App\Http\Controllers\API\RecipeController::class, 'store'])->name('api.recipes.store');
    Route::put('/{recipe}', [App\Http\Controllers\API\RecipeController::class, 'update'])->name('api.recipes.update');
    Route::delete('/{recipe}', [App\Http\Controllers\API\RecipeController::class, 'delete'])->name('api.recipes.delete');
});
