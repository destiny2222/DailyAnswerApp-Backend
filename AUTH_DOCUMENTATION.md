# API Documentation - OTP & Authentication Flow

This document details the updated authentication process, including Cloudflare Turnstile integration and OTP (One-Time Password) verification for both registration and login.

## 1. Prerequisites
- **Cloudflare Turnstile**: All initial authentication requests (Login & Register) require a valid `cf-turnstile-response`.
- **Environment**: Ensure `MAIL_MAILER`, `MAIL_HOST`, etc., are configured in your `.env` to receive OTP emails.

---

## 2. Registration Flow

### Step 1: Create Account
Submit user details and Turnstile response to initiate registration.

- **Endpoint**: `POST /api/v1/register`
- **Request Body**:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "Password123!",
    "password_confirmation": "Password123!",
    "username": "johndoe",
    "cf-turnstile-response": "TURNSTILE_TOKEN"
}
```
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Registration successful. Please verify your email with the OTP sent.",
    "otp_required": true,
    "email": "john@example.com"
}
```

### Step 2: Verify Registration OTP
Submit the 6-digit code received via email to complete registration.

- **Endpoint**: `POST /api/v1/verify-registration-otp`
- **Request Body**:
```json
{
    "email": "john@example.com",
    "otp": "123456"
}
```
- **Success Response (200 OK)**: Returns the Sanctum auth token.

---

## 3. Login Flow

### Step 1: Verify Credentials
Submit email, password, and Turnstile response.

- **Endpoint**: `POST /api/v1/login`
- **Request Body**:
```json
{
    "email": "john@example.com",
    "password": "Password123!",
    "cf-turnstile-response": "TURNSTILE_TOKEN"
}
```
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Credentials verified. Please enter the OTP sent to your email.",
    "otp_required": true,
    "email": "john@example.com"
}
```

### Step 2: Verify Login OTP
Submit the 6-digit code to receive your authentication token.

- **Endpoint**: `POST /api/v1/verify-login-otp`
- **Request Body**:
```json
{
    "email": "john@example.com",
    "otp": "123456"
}
```
- **Success Response (200 OK)**: Returns the Sanctum auth token.

---

## 4. Helper Endpoints

### Resend OTP
Use this if the OTP expires (10 minutes) or is not received.

- **Endpoint**: `POST /api/v1/resend-otp`
- **Request Body**:
```json
{
    "email": "john@example.com",
    "type": "registration" // or "login"
}
```

---

## Security Notes
- **Turnstile Verification**: Turnstile is only required on the first step of login/register to prevent bot attacks. The OTP steps do not require Turnstile as they are already scoped to a verified email/session.
- **OTP Storage**: OTPs are stored in the system cache (Redis/File) and expire automatically after 10 minutes.
- **Rate Limiting**: Login attempts are tracked by email and will be blocked after 5 failed attempts for 15 minutes.
