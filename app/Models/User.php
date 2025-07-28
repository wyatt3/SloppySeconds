<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method static create(array<string, mixed> $array)
 * @property UserGroup $userGroup
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_group_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user group that owns the user.
     *
     * @return BelongsTo<UserGroup, $this>
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    /**
     * Get the recipes for the user.
     *
     * @return HasManyThrough<Recipe, UserGroup, $this>
     */
    public function recipes(): HasManyThrough
    {
        return $this->hasManyThrough(Recipe::class, UserGroup::class, 'id', 'user_group_id', 'user_group_id', 'id');
    }

    /**
     * Get the meals for the user.
     *
     * @return HasManyThrough<Meal, UserGroup, $this>
     */
    public function meals(): HasManyThrough
    {
        return $this->hasManyThrough(Meal::class, UserGroup::class, 'id', 'user_group_id', 'user_group_id', 'id');
    }
}
