<?php

namespace App\Http\Resources;

class InstitutionResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'commercial_name' => $this->commercial_name,
            'tax_id' => $this->tax_id,
            'institution_type' => $this->institution_type,
            'city' => $this->city,
            'active' => $this->active,
        ];
    }
}
