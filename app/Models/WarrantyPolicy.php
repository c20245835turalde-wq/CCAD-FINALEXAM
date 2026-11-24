<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarrantyPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'appliance_id',
        'warranty_expiry',
    ];

    protected function casts(): array
    {
        return [
            'warranty_expiry' => 'date:Y-m-d',
        ];
    }

    public function appliance(): BelongsTo
    {
        return $this->belongsTo(Appliance::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(WarrantyClaim::class);
    }
}
