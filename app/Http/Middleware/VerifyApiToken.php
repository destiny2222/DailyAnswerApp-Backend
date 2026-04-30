<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-API-TOKEN') ?: $request->bearerToken();
        $expectedToken = env('API_ACCESS_TOKEN');

        if (!$token || $token !== $expectedToken) {
            return response()->json([
                'message' => 'Unauthorized: Invalid or missing API token.',
            ], 401);
        }

        return $next($request);
    }
}
