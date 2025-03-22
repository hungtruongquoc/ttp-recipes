<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
}
