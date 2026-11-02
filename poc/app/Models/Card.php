<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Card extends Model
{
    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public const PRIORITIES = [
        'urgente' => 'Urgente',
        'alta' => 'Alta',
        'media' => 'Média',
        'baixa' => 'Baixa',
    ];

    public const PRIORITY_COLORS = [
        'urgente' => 'danger',
        'alta' => 'warning',
        'media' => 'info',
        'baixa' => 'gray',
    ];

    public const ORIGINS = [
        'manual' => 'Manual',
        'recorrencia' => 'Recorrência',
        'agente' => 'Agente',
    ];

    protected static function booted(): void
    {
        static::saving(function (Card $card) {
            if ($card->isDirty('board_column_id') || ! $card->exists) {
                $column = BoardColumn::find($card->board_column_id);
                $card->completed_at = $column?->is_done ? ($card->completed_at ?? now()) : null;
            }
        });
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(BoardColumn::class, 'board_column_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('position');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function monitorIssue(): HasOne
    {
        return $this->hasOne(MonitorIssue::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('completed_at');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->open()->whereDate('due_date', '<', today());
    }

    public function isOverdue(): bool
    {
        return $this->completed_at === null && $this->due_date !== null && $this->due_date->lt(today());
    }
}
