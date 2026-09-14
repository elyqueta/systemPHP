<?php

namespace App\Http\Requests;

use App\Models\Institution;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Institution::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:200'],
            'commercial_name' => ['nullable', 'string', 'max:200'],
            'tax_id' => ['required', 'string', 'max:20', 'unique:institutions,tax_id'],
            'institution_type' => [Rule::in(['LDA', 'SA', 'ENI', 'ONG', 'EP', 'OUTRO'])],
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
