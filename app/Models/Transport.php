<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transport extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'transport_type',
        'departure_location',
        'arrival_location',
        'price_per_person',
        'capacity',
        'available_seats',
        'departure_time',
        'arrival_time',
        'images',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'images' => 'array',
        'price_per_person' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }
}
