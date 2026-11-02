<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $guarded = [];

    protected $casts = ['is_done' => 'boolean'];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
