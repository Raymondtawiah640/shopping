<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tour extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'location',
        'city',
        'country',
        'price_per_person',
        'duration_days',
        'max_participants',
        'available_spots',
        'start_date',
        'end_date',
        'itinerary',
        'images',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'itinerary' => 'array',
        'images' => 'array',
        'price_per_person' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }
}
