<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:200'],
            'commercial_name' => ['nullable', 'string', 'max:200'],
            'tax_id' => ['nullable', 'string', 'max:20', Rule::unique('institutions', 'tax_id')->ignore($this->route('institution'))],
            'institution_type' => ['sometimes', Rule::in(['LDA', 'SA', 'ENI', 'ONG', 'EP', 'OUTRO'])],
            'founding_date' => ['nullable', 'date_format:Y-m-d'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:200'],
            'address' => ['nullable', 'string', 'max:250'],
            'neighborhood' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
        ];
    }
}
