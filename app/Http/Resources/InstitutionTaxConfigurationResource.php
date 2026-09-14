<?php

namespace App\Http\Resources;

class InstitutionTaxConfigurationResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'employee_social_security_rate' => $this->employee_social_security_rate,
            'employer_social_security_rate' => $this->employer_social_security_rate,
            'meal_allowance' => $this->meal_allowance,
            'transport_allowance' => $this->transport_allowance,
            'currency_id' => $this->currency_id,
            'tax_regime' => $this->tax_regime,
        ];
    }
}
