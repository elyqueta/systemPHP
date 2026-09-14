<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\InstitutionBankAccount;
use App\Models\InstitutionTaxConfiguration;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            [
                'name' => 'Kwanza Tech Lda',
                'commercial_name' => 'Kwanza Tech',
                'tax_id' => '5000000001',
                'institution_type' => 'LDA',
                'founding_date' => '2020-01-15',
                'phone' => '923111111',
                'email' => 'kwanza@example.com',
                'website' => 'https://kwanza.ao',
                'address' => 'Rua da Independência, 123',
                'neighborhood' => 'Ingombota',
                'city' => 'Luanda',
                'province_id' => 1,
                'municipality_id' => 1,
            ],
            [
                'name' => 'Soluções Angola SA',
                'commercial_name' => 'SolAngola',
                'tax_id' => '5000000002',
                'institution_type' => 'SA',
                'founding_date' => '2018-06-20',
                'phone' => '923222222',
                'email' => 'solucoes@example.com',
                'website' => 'https://solucoes.ao',
                'address' => 'Avenida 4 de Fevereiro, 456',
                'neighborhood' => 'Miramar',
                'city' => 'Luanda',
                'province_id' => 1,
                'municipality_id' => 1,
            ],
            [
                'name' => 'ONG Esperança',
                'commercial_name' => 'Esperança',
                'tax_id' => '5000000003',
                'institution_type' => 'ONG',
                'founding_date' => '2019-03-10',
                'phone' => '923333333',
                'email' => 'esperanca@example.com',
                'address' => 'Rua da Missão, 789',
                'neighborhood' => 'Sambizanga',
                'city' => 'Luanda',
                'province_id' => 1,
                'municipality_id' => 1,
            ],
        ];

        foreach ($institutions as $data) {
            $institution = Institution::firstOrCreate(
                ['tax_id' => $data['tax_id']],
                $data
            );

            InstitutionTaxConfiguration::updateOrCreate(
                ['institution_id' => $institution->id],
                [
                    'employee_social_security_rate' => 3.00,
                    'employer_social_security_rate' => 8.00,
                    'meal_allowance' => 15000.00,
                    'transport_allowance' => 10000.00,
                    'currency_id' => 1,
                    'tax_regime' => 'Geral',
                ]
            );

            InstitutionBankAccount::updateOrCreate(
                ['institution_id' => $institution->id, 'account_number' => '1234567890'],
                [
                    'bank_id' => 1,
                    'bank_name' => 'Banco Nacional de Angola',
                    'account_number' => '1234567890',
                    'iban' => 'AO0600123456789012345678',
                    'currency_id' => 1,
                    'is_primary' => true,
                ]
            );
        }
    }
}
