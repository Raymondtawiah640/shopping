<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restaurant extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'location',
        'city',
        'country',
        'cuisine_type',
        'average_price',
        'capacity',
        'opening_hours',
        'images',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'images' => 'array',
        'average_price' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }
}
