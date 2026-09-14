<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_social_security_rate' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'employer_social_security_rate' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'meal_allowance' => ['sometimes', 'numeric', 'min:0'],
            'transport_allowance' => ['sometimes', 'numeric', 'min:0'],
            'currency_id' => ['sometimes', 'integer'],
            'tax_regime' => ['sometimes', 'string', 'max:50'],
        ];
    }
}
