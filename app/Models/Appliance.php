<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'appliance_type',
        'model_number',
        'serial_number',
        'purchase_date',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function warrantyPolicy(): HasOne
    {
        return $this->hasOne(WarrantyPolicy::class);
    }
}
