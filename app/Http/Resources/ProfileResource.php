<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name ?? null,
            'email' => $this->email ?? null,
            'username' => $this->username ?? null,
            'profile_image_url' => $this->profile_photo_url ? asset('profile/'.$this->profile_photo_url) : null,
            'has_paid' => $this->has_paid ?? false,
            'payment_status' => $this->payment_status ?? null,
            'payment_date' => $this->payment_date ?? null,
            'payment_expires_at' => $this->payment_expires_at ?? null,
            'stripe_customer_id' => $this->stripe_customer_id ?? null,
            'stripe_subscription_id' => $this->stripe_subscription_id ?? null,
        ];
    }
}
