<?php

namespace App\Traits;

trait EnumFromName
{
    public static function tryFrom(string $name): ?static
    {
        return array_column(static::cases(), null, 'name')[$name] ?? null;
    }
}
