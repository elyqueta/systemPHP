<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculatePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gross_salary' => ['required', 'numeric', 'min:0'],
            'reference_date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
