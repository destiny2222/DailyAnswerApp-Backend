<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'interval',
        'payment_intent_id',
        'subscription_id',
        'status',
        'paid_at',
        'cancelled_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the user that made the support payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active recurring payments.
     */
    public function scopeActiveRecurring($query)
    {
        return $query->where('type', 'recurring')
            ->where('status', 'completed')
            ->whereNull('cancelled_at');
    }

    /**
     * Scope a query to only include one-time payments.
     */
    public function scopeOneTime($query)
    {
        return $query->where('type', 'one_time');
    }
}
