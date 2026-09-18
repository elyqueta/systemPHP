<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class IrtBracketResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'effective_from' => $this->effective_from?->format('Y-m-d'),
            'bracket_order' => $this->bracket_order,
            'lower_bound' => (string) $this->lower_bound,
            'upper_bound' => $this->upper_bound === null ? null : (string) $this->upper_bound,
            'rate' => (string) $this->rate,
            'fixed_amount' => (string) $this->fixed_amount,
        ];
    }
}
