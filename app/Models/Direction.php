<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $content
 * @property int $recipe_id
 * @property int $order
 * @property ?string $image
 * @property-read string $imagePath
 * @property-read Recipe $recipe
 */
class Direction extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\DirectionFactory> */
    use HasFactory;

    protected $table = 'directions';

    /** @var list<string> */
    protected $appends = ['imagePath'];

    protected $fillable = [
        'content',
        'recipe_id',
        'order',
        'image',
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

    /**
     * Get the image path
     *
     * @return ?string
     */
    public function getImagePathAttribute(): ?string
    {
        return route('direction.image', ['direction' => $this->getKey()]);
    }
}
