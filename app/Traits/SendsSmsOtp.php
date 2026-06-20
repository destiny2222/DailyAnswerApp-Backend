<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait SendsSmsOtp
{
    /**
     * Send OTP via Termii SMS
     */
    private function sendOtpWithTermii(string $phone, string $otp, ?string $message = null)
    {
        $formattedPhone = $this->formatPhone($phone);
        
        if (!$message) {
            $message = "Your DailyAnswer OTP is: {$otp}. Valid for 10 minutes. Do not share.";
        }

        $payload = [
            'to' => $formattedPhone,
            'from' => config('services.termii.sender_id'),
            'sms' => $message,
            'api_key' => config('services.termii.api_key'),
            'type' => 'plain',
            'channel' => 'generic',
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(config('services.termii.base_url') . '/api/sms/send', $payload);

        $result = $response->json();

        if ($response->successful() && (isset($result['message_id']) || (isset($result['status']) && $result['status'] === 'success'))) {
            return $result;
        } else {
            Log::error('Termii OTP Error', ['error' => $result['message'] ?? 'Unknown error']);
            throw new \Exception('Failed to send OTP via Termii: ' . ($result['message'] ?? 'Unknown error'));
        }
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '+234' . substr($phone, 1);
        } elseif (substr($phone, 0, 3) === '234') {
            $phone = '+' . $phone;
        }

        return $phone;
    }
}
