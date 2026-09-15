<?php

namespace Database\Seeders;

use App\Models\TaxIrtBracket;
use Illuminate\Database\Seeder;

class TaxIrtBracketSeeder extends Seeder
{
    public function run(): void
    {
        $effectiveFrom = '2026-01-01';

        $brackets = [
            [1,            0,         150000, 0.00,  0],
            [2,     150000.01,       200000, 16.00, 12500],
            [3,     200000.01,       300000, 18.00, 31250],
            [4,     300000.01,       500000, 19.00, 49250],
            [5,     500000.01,     1000000, 20.00, 87250],
            [6,   1000000.01,     1500000, 21.00, 187250],
            [7,   1500000.01,     2000000, 22.00, 292250],
            [8,   2000000.01,     2500000, 23.00, 402250],
            [9,   2500000.01,     5000000, 24.00, 517250],
            [10,  5000000.01,    10000000, 24.50, 1117250],
            [11, 10000000.01,      null,    25.00, 2342250],
        ];

        foreach ($brackets as [$order, $lower, $upper, $rate, $fixed]) {
            TaxIrtBracket::updateOrCreate(
                ['effective_from' => $effectiveFrom, 'bracket_order' => $order],
                [
                    'lower_bound' => $lower,
                    'upper_bound' => $upper,
                    'rate' => $rate,
                    'fixed_amount' => $fixed,
                ],
            );
        }
    }
}
