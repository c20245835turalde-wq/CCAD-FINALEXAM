<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WarrantyClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_policy_id',
        'claim_date',
        'defect_description',
        'claim_status',
    ];

    protected function casts(): array
    {
        return [
            'claim_date' => 'date',
        ];
    }

    public function warrantyPolicy(): BelongsTo
    {
        return $this->belongsTo(WarrantyPolicy::class);
    }

    public function payout(): HasOne
    {
        return $this->hasOne(ClaimPayout::class);
    }
}
