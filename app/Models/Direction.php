<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Direction extends Model
{
    protected $table = 'directions';

    protected $fillable = [
        'content',
        'recipe_id',
        'order',
    ];

    /**
     * Get the recipe that owns the direction.
     *
     * @return BelongsTo<Recipe, $this>
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
