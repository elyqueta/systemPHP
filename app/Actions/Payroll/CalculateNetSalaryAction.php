<?php

namespace App\Actions\Payroll;

use App\Models\Institution;
use App\Models\TaxIrtBracket;
use RuntimeException;

class CalculateNetSalaryAction
{
    public function execute(Institution $institution, float $grossSalary, ?string $referenceDate = null): array
    {
        $taxConfig = $institution->taxConfiguration;

        $employeeRate = (float) ($taxConfig->employee_social_security_rate ?? 3.00);
        $employerRate = (float) ($taxConfig->employer_social_security_rate ?? 8.00);

        $employeeSocialSecurity = $grossSalary * $employeeRate / 100;
        $employerSocialSecurity = $grossSalary * $employerRate / 100;

        $taxableIncome = $grossSalary - $employeeSocialSecurity;

        $bracket = TaxIrtBracket::query()
            ->effectiveOn($referenceDate)
            ->where('lower_bound', '<=', $taxableIncome)
            ->where(function ($query) use ($taxableIncome) {
                $query->whereNull('upper_bound')
                    ->orWhere('upper_bound', '>=', $taxableIncome);
            })
            ->first();

        if (! $bracket) {
            $effectiveFrom = TaxIrtBracket::query()
                ->where('effective_from', '<=', ($referenceDate ?? now()))
                ->max('effective_from');

            throw new RuntimeException('Não foi possível determinar o escalão de IRT aplicável para o rendimento tributável ' . $taxableIncome . '. Verifique se a tabela tax_irt_brackets tem dados para a data ' . ($referenceDate ?? 'hoje') . '.');
        }

        $irtAmount = (float) $bracket->fixed_amount
            + ($taxableIncome - (float) $bracket->lower_bound) * ((float) $bracket->rate / 100);

        $netSalary = $grossSalary - $employeeSocialSecurity - $irtAmount;

        return [
            'gross_salary' => number_format($grossSalary, 2, '.', ''),
            'employee_social_security' => number_format($employeeSocialSecurity, 2, '.', ''),
            'employer_social_security' => number_format($employerSocialSecurity, 2, '.', ''),
            'taxable_income' => number_format($taxableIncome, 2, '.', ''),
            'irt_bracket_order' => $bracket->bracket_order,
            'irt_rate' => (float) $bracket->rate,
            'irt_amount' => number_format($irtAmount, 2, '.', ''),
            'net_salary' => number_format($netSalary, 2, '.', ''),
        ];
    }
}
