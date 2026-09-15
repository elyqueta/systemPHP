<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class TaxIrtBracket extends Model
{
    use HasUuid;

    protected $fillable = [
        'effective_from',
        'bracket_order',
        'lower_bound',
        'upper_bound',
        'rate',
        'fixed_amount',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'lower_bound' => 'decimal:2',
            'upper_bound' => 'decimal:2',
            'rate' => 'decimal:2',
            'fixed_amount' => 'decimal:2',
        ];
    }

    public function scopeEffectiveOn(Builder $query, \DateTimeInterface|string|null $date = null): Builder
    {
        $date = $date ?? now();

        $effectiveFrom = static::query()
            ->where('effective_from', '<=', $date)
            ->max('effective_from');

        if ($effectiveFrom === null) {
            $effectiveFrom = static::query()->min('effective_from');
        }

        if ($effectiveFrom === null) {
            throw new RuntimeException('Nenhuma versão da tabela de IRT está configurada na base de dados.');
        }

        return $query->where('effective_from', $effectiveFrom);
    }
}
