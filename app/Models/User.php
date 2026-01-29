<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,  HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'has_paid',
        'profile_photo_url',
        'payment_status',
        'payment_date',
        'payment_expires_at',
        'stripe_customer_id',
        'stripe_subscription_id',
        'subscription_plan',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_paid' => 'boolean',
            'payment_date' => 'datetime',
            'payment_expires_at' => 'datetime',
        ];
    }

    /**
     * Check if user has valid payment.
     */
    public function hasPaid(): bool
    {
        if (! $this->has_paid) {
            return false;
        }

        if ($this->payment_expires_at === null) {
            return true;
        }

        return $this->payment_expires_at->isFuture();
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function prayerNotes()
    {
        return $this->hasMany(PrayerNote::class);
    }

    public function supportPayments()
    {
        return $this->hasMany(SupportPayment::class);
    }
}
