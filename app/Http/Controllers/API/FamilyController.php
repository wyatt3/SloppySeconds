<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use App\Services\FamilyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FamilyController extends Controller
{
    public function __construct(private FamilyService $familyService) {}

    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $family = $this->familyService->getFamilyDetails($user);

        if (!$family) {
            return response(['in_family' => false]);
        }

        return response([
            'in_family' => true,
            'family' => [
                'id' => $family->getKey(),
                'join_code' => $family->join_code,
                'members' => $family->users()->select('id', 'name', 'email')->get(),
                'created_at' => $family->created_at,
            ],
        ]);
    }

    public function create(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->userGroup) {
            return response(['error' => 'You are already in a family'], 400);
        }

        $family = $this->familyService->createFamily($user);

        return response([
            'in_family' => true,
            'family' => [
                'id' => $family->getKey(),
                'join_code' => $family->join_code,
                'members' => $family->users()->select('id', 'name', 'email')->get(),
                'created_at' => $family->created_at,
            ],
        ], 201);
    }

    public function join(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->userGroup) {
            return response(['error' => 'You are already in a family'], 400);
        }

        $joinCode = $request->input('join_code');

        try {
            $family = $this->familyService->joinFamily($user, $joinCode);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response(['error' => 'Invalid join code'], 400);
        }

        return response([
            'in_family' => true,
            'family' => [
                'id' => $family->getKey(),
                'join_code' => $family->join_code,
                'members' => $family->users()->select('id', 'name', 'email')->get(),
                'created_at' => $family->created_at,
            ],
        ], 201);
    }

    public function leave(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->userGroup) {
            return response(['error' => 'You are not in a family'], 400);
        }

        $this->familyService->leaveFamily($user);

        return response(['in_family' => false]);
    }
}