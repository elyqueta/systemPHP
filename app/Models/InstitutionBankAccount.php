<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionBankAccount extends Model
{
    use HasUuid;

    protected $fillable = [
        'institution_id',
        'bank_id',
        'bank_name',
        'account_number',
        'iban',
        'currency_id',
        'is_primary',
    ];

    public $timestamps = false;

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
