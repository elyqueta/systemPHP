<?php

namespace App\Actions\Institutions;

use App\Models\Institution;
use App\Models\InstitutionTaxConfiguration;

class UpdateTaxConfigurationAction
{
    public function execute(Institution $institution, array $data): InstitutionTaxConfiguration
    {
        return $institution->taxConfiguration()->updateOrCreate(
            ['institution_id' => $institution->id],
            $data,
        );
    }
}
