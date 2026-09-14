<?php

namespace App\Http\Resources;

class InstitutionBankAccountResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'bank_id' => $this->bank_id,
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'iban' => $this->iban,
            'currency_id' => $this->currency_id,
            'is_primary' => $this->is_primary,
        ];
    }
}
