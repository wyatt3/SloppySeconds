<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $appends = ['directions'];

    protected $fillable = [
        'name',
        'description',
        'image',
        'user_group_id'
    ];

    /**
     * Get all of the directions for the recipe.
     *
     * @return HasMany<Direction, $this>
     */
    public function directions(): HasMany
    {
        return $this->hasMany(Direction::class);
    }

    /**
     * Get all of the directions for the recipe in order.
     *
     * @return Collection<int, Direction>
     */
    public function getDirectionsAttribute()
    {
        return $this->directions()->orderBy('order')->get();
    }
}
