<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application home.
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(): \Inertia\Response|\Inertia\ResponseFactory|\Illuminate\Http\RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('recipes.index');
        }
        return inertia('welcome');
    }
}
