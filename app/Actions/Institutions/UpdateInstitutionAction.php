<?php

namespace App\Actions\Institutions;

use App\Models\Institution;

class UpdateInstitutionAction
{
    public function execute(Institution $institution, array $data): Institution
    {
        $institution->update($data);

        return $institution;
    }
}
