<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meal extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\RecipeFactory> */
    use HasFactory;

    protected $table = 'meals';

    protected $fillable = [
        'date',
        'user_group_id',
    ];


    /**
     * Get the recipes for the meal.
     *
     * @return BelongsToMany<Recipe, $this>
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'meal_recipes');
    }

    /**
     * Get the user group that owns the meal.
     *
     * @return BelongsTo<UserGroup, $this>
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }
}
