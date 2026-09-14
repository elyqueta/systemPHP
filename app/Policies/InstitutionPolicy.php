<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, Institution $institution): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->institutions()
            ->where('institutions.id', $institution->id)
            ->wherePivot('active', true)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Institution $institution): bool
    {
        return $user->hasRole('super_admin');
    }

    public function activate(User $user, Institution $institution): bool
    {
        return $user->hasRole('super_admin');
    }

    public function deactivate(User $user, Institution $institution): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Institution $institution): bool
    {
        return false;
    }
}
