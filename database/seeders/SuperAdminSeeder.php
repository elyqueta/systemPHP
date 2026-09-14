<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'institution_manager']);

        $admin = User::where('email', 'admin@system.ao')->first();

        if (! $admin) {
            $admin = User::create([
                'email' => 'admin@system.ao',
                'name' => 'Administrador da Plataforma',
                'password' => Hash::make(config('app.seed_admin_password', 'ChangeMe123!')),
            ]);
        }

        $admin->assignRole('super_admin');
    }
}
