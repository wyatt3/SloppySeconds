<?php

use App\Http\Middleware\EnsureUserOwnsRecipe;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(Authenticate::class)->group(function () {
    Route::get('/recipes', [App\Http\Controllers\RecipeController::class, 'index'])->name('recipes.index');

    Route::prefix('/recipes/{recipe}')->middleware(EnsureUserOwnsRecipe::class)->group(function () {
        Route::get('/image', [App\Http\Controllers\API\ImageController::class, 'getRecipeImage'])->name('recipe.image');
        Route::get('/directions/{direction}/image', [App\Http\Controllers\API\ImageController::class, 'getDirectionImage'])->name('direction.image');
    });
});
