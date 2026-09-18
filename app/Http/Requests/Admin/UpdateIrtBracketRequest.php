<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIrtBracketRequest extends SuperAdminRequest
{
    public function rules(): array
    {
        return [
            'effective_from' => ['required', 'date_format:Y-m-d'],
            'bracket_order' => ['required', 'integer', 'min:1', 'max:99'],
            'lower_bound' => ['required', 'numeric', 'min:0'],
            'upper_bound' => ['nullable', 'numeric', 'min:0', 'gt:lower_bound'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'fixed_amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
