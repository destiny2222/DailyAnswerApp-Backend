<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MFACustomController extends Controller
{
    /**
     * Generate a new secret key for the user.
     */
    public function setup(Request $request)
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        $secretKey = $google2fa->generateSecretKey();
        
        // Temporarily store secret in user object (not saved until verified)
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secretKey
        );

        return response()->json([
            'secret' => $secretKey,
            'qr_code_url' => $qrCodeUrl
        ]);
    }

    /**
     * Activate MFA after verifying the TOTP.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($request->secret, $request->code)) {
            $user->google2fa_secret = $request->secret;
            
            // Generate recovery codes
            $backupCodes = [];
            for ($i = 0; $i < 8; $i++) {
                $backupCodes[] = Str::random(10);
            }
            $user->backup_codes = encrypt(json_encode($backupCodes));
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'MFA activated successfully.',
                'backup_codes' => $backupCodes
            ]);
        }

        return response()->json(['errors' => ['Invalid verification code.']], 422);
    }

    /**
     * Verify TOTP code during login (step 2).
     */
    public function verify(Request $request)
    {
        // This would be used if login is 2-step
        $request->validate(['code' => 'required|string|size:6']);
        $user = Auth::user();

        $google2fa = new Google2FA();
        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
             // Return login response (or set session/new token)
             return response()->json(['success' => true]);
        }

        return response()->json(['errors' => ['Invalid verification code.']], 422);
    }
}
