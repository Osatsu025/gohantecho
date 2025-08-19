<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Time extends Model
{
    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }
}
