<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HandleApiEncryption
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = config('app.api_encryption_key') ?? env('API_ENCRYPTION_KEY');

        if (!$key) {
            return $next($request);
        }

        // 1. Decrypt incoming request if it contains encrypted data
        if ($request->has('encrypted_data')) {
            try {
                $decrypted = $this->decrypt($request->input('encrypted_data'), $key);
                $request->merge(json_decode($decrypted, true) ?? []);
            } catch (\Exception $e) {
                Log::error('API Decryption Failed: ' . $e->getMessage());
                return response()->json(['error' => 'Invalid encrypted payload'], 400);
            }
        }

        $response = $next($request);

        // 2. Encrypt outgoing response if it's JSON
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            try {
                $encrypted = $this->encrypt(json_encode($data), $key);
                $response->setData(['data' => $encrypted]);
            } catch (\Exception $e) {
                Log::error('API Encryption Failed: ' . $e->getMessage());
            }
        }

        return $response;
    }

    private function encrypt($data, $key)
    {
        $cipher = "aes-256-gcm";
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $tag = "";
        $ciphertext = openssl_encrypt($data, $cipher, $key, $options = 0, $iv, $tag);
        
        return base64_encode($iv . $tag . $ciphertext);
    }

    private function decrypt($data, $key)
    {
        $cipher = "aes-256-gcm";
        $decoded = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher);
        $taglen = 16;
        $iv = substr($decoded, 0, $ivlen);
        $tag = substr($decoded, $ivlen, $taglen);
        $ciphertext = substr($decoded, $ivlen + $taglen);
        
        return openssl_decrypt($ciphertext, $cipher, $key, $options = 0, $iv, $tag);
    }
}
