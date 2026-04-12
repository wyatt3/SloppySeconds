<?php

namespace App\Http\Controllers;

class FamilyController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Family/Index');
    }
}