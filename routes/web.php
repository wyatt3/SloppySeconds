<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/recipes/{recipe}/image', [App\Http\Controllers\API\ImageController::class, 'getRecipeImage'])->name('recipe.image');
