<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_claim_id',
        'approved_amount',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function warrantyClaim(): BelongsTo
    {
        return $this->belongsTo(WarrantyClaim::class);
    }
}
