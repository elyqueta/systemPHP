<?php

namespace App\Actions\Institutions;

use App\Models\Institution;
use App\Models\InstitutionBankAccount;

class CreateBankAccountAction
{
    public function execute(Institution $institution, array $data): InstitutionBankAccount
    {
        return $institution->bankAccounts()->create($data);
    }
}
