<?php

use App\Http\Middleware\EnsureUserCanAccessImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/recipes/{recipe}/image', [App\Http\Controllers\API\ImageController::class, 'getRecipeImage'])
    ->middleware(EnsureUserCanAccessImage::class)
    ->name('recipe.image');

Route::get('/directions/{direction}/image', [App\Http\Controllers\API\ImageController::class, 'getDirectionImage'])
    ->middleware(EnsureUserCanAccessImage::class)
    ->name('direction.image');

Route::get('/recipes', [App\Http\Controllers\API\RecipeController::class, 'index'])->name('api.recipes.index');
