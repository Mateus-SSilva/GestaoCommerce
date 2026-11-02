<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    protected $guarded = [];

    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class);
    }
}
