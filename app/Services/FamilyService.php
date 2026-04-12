<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Str;

class FamilyService
{
    public function createFamily(User $user): UserGroup
    {
        $family = UserGroup::create([
            'join_code' => $this->generateUniqueJoinCode(),
        ]);

        $user->update(['user_group_id' => $family->getKey()]);

        return $family;
    }

    public function joinFamily(User $user, string $joinCode): UserGroup
    {
        $family = UserGroup::where('join_code', $joinCode)->firstOrFail();

        $user->update(['user_group_id' => $family->getKey()]);

        return $family;
    }

    public function leaveFamily(User $user): void
    {
        $user->update(['user_group_id' => null]);
    }

    public function getFamilyDetails(User $user): ?UserGroup
    {
        return $user->userGroup;
    }

    private function generateUniqueJoinCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (UserGroup::where('join_code', $code)->exists());

        return $code;
    }
}