<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class HospitalityBooking extends Model
{
    protected $fillable = [
        'booking_id',
        'customer_id',
        'vendor_id',
        'service_type',
        'service_id',
        'number_of_guests',
        'check_in_date',
        'check_out_date',
        'booking_date',
        'special_requests',
        'total_amount',
        'status',
        'vendor_notes',
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'check_out_date' => 'datetime',
        'booking_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_id)) {
                $booking->booking_id = 'BOOK-' . strtoupper(Str::random(8));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    // Polymorphic relationship to different service types
    public function service()
    {
        return $this->morphTo();
    }
}
