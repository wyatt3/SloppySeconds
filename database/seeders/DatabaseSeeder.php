<?php

namespace Database\Seeders;

use App\Models\Direction;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserGroup;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $userGroup = UserGroup::factory()->create();

        $user = User::factory()->for($userGroup)->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('1234')
        ]);

        Recipe::factory()
            ->count(3)
            ->for($userGroup)
            ->has(
                Direction::factory()->count(3)
            )->create();

        Meal::factory()
            ->count(3)
            ->for($userGroup)
            ->has(
                Recipe::factory()->has(
                    Ingredient::factory()->count(3)
                )
            )
            ->create([
                'date' => now(),
            ]);
    }
}
