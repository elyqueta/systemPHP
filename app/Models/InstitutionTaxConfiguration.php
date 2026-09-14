<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionTaxConfiguration extends Model
{
    protected $primaryKey = 'institution_id';

    public $incrementing = false;

    protected $fillable = [
        'institution_id',
        'employee_social_security_rate',
        'employer_social_security_rate',
        'meal_allowance',
        'transport_allowance',
        'currency_id',
        'tax_regime',
    ];

    public $timestamps = false;

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
