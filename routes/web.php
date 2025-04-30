<?php

use App\Http\Middleware\EnsureUserOwnsRecipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('/recipes/{recipe}')->middleware(EnsureUserOwnsRecipe::class)->group(function () {
    Route::get('/image', [App\Http\Controllers\API\ImageController::class, 'getRecipeImage'])->name('recipe.image');
    Route::get('/directions/{direction}/image', [App\Http\Controllers\API\ImageController::class, 'getDirectionImage'])->name('direction.image');
});
