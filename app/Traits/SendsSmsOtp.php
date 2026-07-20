<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait SendsSmsOtp
{
    /**
     * Send OTP via SMSLive247 SMS
     */
    private function sendOtpWithSmsLive247(string $phone, string $otp, ?string $message = null)
    {
        $formattedPhone = $this->formatPhone($phone);
        
        if (!$message) {
            $message = "Your DailyAnswer OTP is: {$otp}. Valid for 10 minutes. Do not share.";
        }

        $payload = [
            'senderID' => config('services.smslive247.sender_id'),
            'messageText' => $message,
            'mobileNumber' => $formattedPhone,
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.smslive247.api_key'),
        ])->post(config('services.smslive247.base_url') . '/api/v4/sms', $payload);

        $result = $response->json();

        if ($response->successful() && (isset($result['messageID']) || isset($result['batchID']))) {
            return $result;
        } else {
            $errorMessage = $result['message'] ?? 'Unknown error';
            Log::error('SMSLive247 OTP Error', ['error' => $errorMessage, 'response' => $result]);
            throw new \Exception('Failed to send OTP via SMSLive247: ' . $errorMessage);
        }
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '234' . substr($phone, 1);
        }

        return $phone;
    }
}
