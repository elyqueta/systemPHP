<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Institution extends Model
{
    use HasUuid;

    protected $fillable = [
        'name', 'commercial_name', 'tax_id', 'institution_type',
        'founding_date', 'phone', 'email', 'website',
        'address', 'neighborhood', 'city', 'province_id', 'municipality_id',
        'active',
    ];

    public function taxConfiguration(): HasOne
    {
        return $this->hasOne(InstitutionTaxConfiguration::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(InstitutionBankAccount::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'institution_user')
            ->withPivot('active');
    }
}
