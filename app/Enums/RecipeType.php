<?php

namespace App\Enums;

use App\Traits\EnumFromName;

enum RecipeType
{
    use EnumFromName;
    case Entree;
    case Side;
    case Dessert;
    case Drink;
}
