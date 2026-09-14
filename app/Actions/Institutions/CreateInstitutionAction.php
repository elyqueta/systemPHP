<?php

namespace App\Actions\Institutions;

use App\Models\Institution;

class CreateInstitutionAction
{
    public function execute(array $data): Institution
    {
        return Institution::create($data);
    }
}
