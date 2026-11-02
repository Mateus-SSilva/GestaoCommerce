<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringTask extends Model
{
    protected $guarded = [];

    protected $casts = [
        'weekdays' => 'array',
        'checklist' => 'array',
        'is_active' => 'boolean',
        'last_generated_on' => 'date',
    ];

    public const FREQUENCIES = [
        'diaria' => 'Diária (dias úteis)',
        'semanal' => 'Semanal',
        'mensal' => 'Mensal',
    ];

    public const WEEKDAYS = [
        1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado', 7 => 'Domingo',
    ];

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

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    /** A regra se aplica a esta data? */
    public function isDueOn(CarbonInterface $date): bool
    {
        return match ($this->frequency) {
            'diaria' => $date->isWeekday(),
            'semanal' => in_array($date->dayOfWeekIso, array_map('intval', $this->weekdays ?? []), true),
            'mensal' => $date->day === min((int) $this->day_of_month, $date->daysInMonth),
            default => false,
        };
    }

    /** Próximas N datas em que a tarefa será criada (para exibir na tela). */
    public function nextOccurrences(int $count = 3): array
    {
        $dates = [];
        $date = today();
        for ($i = 0; $i < 400 && count($dates) < $count; $i++, $date = $date->copy()->addDay()) {
            if ($this->isDueOn($date) && (! $this->last_generated_on || $date->gt($this->last_generated_on))) {
                $dates[] = $date->copy();
            }
        }

        return $dates;
    }

    public function describe(): string
    {
        return match ($this->frequency) {
            'diaria' => 'Todo dia útil',
            'semanal' => 'Toda '.collect($this->weekdays ?? [])->map(fn ($d) => mb_strtolower(self::WEEKDAYS[(int) $d] ?? ''))->join(', ', ' e '),
            'mensal' => 'Todo dia '.$this->day_of_month.' do mês',
            default => '-',
        };
    }
}
