<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_id' => ['nullable', 'integer'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['required', 'string', 'max:50'],
            'iban' => ['nullable', 'string', 'max:34'],
            'currency_id' => ['nullable', 'integer'],
            'is_primary' => ['boolean'],
        ];
    }
}
