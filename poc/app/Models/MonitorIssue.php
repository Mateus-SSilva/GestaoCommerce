<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorIssue extends Model
{
    protected $guarded = [];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public const SEVERITIES = [
        'critico' => 'Crítico',
        'alto' => 'Alto',
        'medio' => 'Médio',
        'info' => 'Informativo',
    ];

    public const SEVERITY_COLORS = [
        'critico' => 'danger',
        'alto' => 'warning',
        'medio' => 'info',
        'info' => 'gray',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
