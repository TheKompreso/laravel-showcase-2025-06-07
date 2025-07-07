<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $user_id
 */
class Booking extends Model
{
    protected $fillable = [
        'user_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($booking) {
            $booking->bookingSlots()->delete();
        });
    }

    public function bookingSlots(): HasMany
    {
        return $this->hasMany(BookingSlot::class);
    }
}
