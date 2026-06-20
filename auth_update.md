# Authentication Updates: Termii SMS OTP & Cloudflare Turnstile

This document outlines the recent integration of **Termii SMS OTP** (replacing the previous email-based OTP system) and **Cloudflare Turnstile** (replacing Google reCAPTCHA) for user authentication.

---

## 1. Environment Configurations (`.env`)

Ensure the following variables are defined in your `.env` file:

```env
# Termii SMS Settings
TERMII_API_KEY=your_termii_api_key
TERMII_SENDER_ID=your_approved_sender_id
TERMII_BASE_URL=https://v3.api.termii.com

# Cloudflare Turnstile Settings
TURNSTILE_SITE_KEY=your_turnstile_site_key
TURNSTILE_SECRET_KEY=your_turnstile_secret_key
```

---

## 2. Codebase Modifications

### A. Core Integrations & Configuration
* **Services Config** (`config/services.php`):
  * Replaced the `recaptcha` configuration block with `turnstile` configuration referencing `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY`.
* **Turnstile Custom Rule** (`app/Providers/AppServiceProvider.php`):
  * Replaced the `recaptcha` validation extension with a `turnstile` validation rule that submits verification requests to Cloudflare (`https://challenges.cloudflare.com/turnstile/v0/siteverify`).
* **SMS Dispatch Trait** (`app/Traits/SendsSmsOtp.php`):
  * Introduced a reusable trait implementing `sendOtpWithTermii` and `formatPhone` (formats phone inputs to international standard `+234...`).

### B. User Registration, Login & Verification
* **Database Schema**:
  * Executed migration `add_phone_to_users_table.php` to add a unique, nullable `phone` field to the `users` table.
  - Added `phone` to the `$fillable` array in the `User` model (`app/Models/User.php`).
* **Registration Flow** (`RegisterController.php`):
  * Validates and requires the new `phone` attribute.
  * Captcha parameter changed from `g-recaptcha-response` to `cf-turnstile-response` validated with the `turnstile` rule.
  * Dispatches the initial verification OTP via Termii SMS to the user's phone.
* **Login Flow** (`LoginController.php`):
  * Captcha parameter changed to `cf-turnstile-response` validated with `turnstile`.
  * Verifies the user has a phone number registered (returns 422 error otherwise).
  * Sends the login verification OTP to the user's phone via Termii SMS.
* **Password Reset** (`ResetPasswordController.php`):
  * Captcha parameter changed to `cf-turnstile-response` validated with `turnstile`.
  * Verifies the user has a phone number registered.
  * Sends the password reset OTP to the user's phone via Termii SMS.
* **OTP Re-sending** (`OtpController.php`):
  * Verifies the user has a phone number registered.
  * Resends verification OTPs via Termii SMS.

### C. Views
* **Admin Login Layout** (`resources/views/admin/auth/login.blade.php`):
  * Replaced the Google reCAPTCHA JS tag with Cloudflare Turnstile script: `https://challenges.cloudflare.com/turnstile/v0/api.js`.
  * Updated the widget widget render to `<div class="cf-turnstile" ...>` and verification error feedback to check `cf-turnstile-response`.

---

## 3. Automated Tests & Verification

The test suite validates both Turnstile verification mocking and Termii SMS dispatch:

### Runs and Results
* **Password Reset OTP Test** (`tests/Feature/Auth/PasswordResetOtpTest.php`):
  * Verifies password reset requests mock Cloudflare's siteverify endpoint and successfully send SMS OTPs.
  * Run command: `php artisan test tests/Feature/Auth/PasswordResetOtpTest.php`
* **SMS Auth OTP Test** (`tests/Feature/Auth/SmsAuthOtpTest.php`):
  * Tests registration, login, login failures for accounts without phone numbers, OTP verification, and resending.
  * Run command: `php artisan test tests/Feature/Auth/SmsAuthOtpTest.php`

To run all authentication feature tests:
```bash
php artisan test tests/Feature/Auth
```
