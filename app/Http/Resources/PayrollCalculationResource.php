<?php

namespace App\Http\Resources;

class PayrollCalculationResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'gross_salary' => $this['gross_salary'],
            'employee_social_security' => $this['employee_social_security'],
            'employer_social_security' => $this['employer_social_security'],
            'taxable_income' => $this['taxable_income'],
            'irt_bracket_order' => $this['irt_bracket_order'],
            'irt_rate' => $this['irt_rate'],
            'irt_amount' => $this['irt_amount'],
            'net_salary' => $this['net_salary'],
        ];
    }
}
