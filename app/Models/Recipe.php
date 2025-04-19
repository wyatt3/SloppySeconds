<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read \Illuminate\Support\Collection<int, \App\Models\Direction> $orderedDirections
 */
class Recipe extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\RecipeFactory> */
    use HasFactory;

    protected $table = 'recipes';

    /** @var list<string> $appends */
    protected $appends = ['orderedDirections'];

    protected $fillable = [
        'name',
        'description',
        'image',
        'user_group_id'
    ];

    /**
     * Get the user group that owns the recipe.
     *
     * @return BelongsTo<UserGroup, $this>
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }

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
    public function getOrderedDirectionsAttribute(): Collection
    {
        return $this->directions()->orderBy('order')->get();
    }
}
