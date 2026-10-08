<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


# BizzCRM V3 API Authentication Documentation

This document describes all the Authentication and Profile endpoints available in the BizzCRM API.

**Base URL**: `http://your-domain.com/api`  
**Headers**: For all API requests, include:
```http
Accept: application/json
Content-Type: application/json
```

---

## Table of Contents
1. [Register Company/Tenant (POST /register)](#1-register-companytenant-post-register)
2. [Login with Password (POST /login)](#2-login-with-password-post-login)
3. [Search Country, State, City (GET /search-country)](#3-search-country-state-city-get-search-country)
4. [Get Countries List (GET /countries)](#4-get-countries-list-get-countries)
5. [Get States List (GET /states or /countries/{country_id}/states)](#5-get-states-list-get-states-or-countriescountry_idstates)
6. [Get Cities List (GET /cities or /states/{state_id}/cities)](#6-get-cities-list-get-cities-or-statesstate_idcities)
7. [Get Master Industries (GET /industries)](#7-get-master-industries-get-industries)
8. [Resend Verification Email (POST /resend-verification-email)](#8-resend-verification-email-post-resend-verification-email)
9. [Send OTP (POST /send-otp)](#9-send-otp-post-send-otp)
10. [Verify OTP (POST /verify-otp)](#10-verify-otp-post-verify-otp)
11. [Forgot Password Link Request (POST /forget-email)](#11-forgot-password-link-request-post-forget-email)
12. [Submit Reset Password (POST /reset-password-submit)](#12-submit-reset-password-post-reset-password-submit)
13. [Verify Email Token (GET /verify-email/{token})](#13-verify-email-token-get-verify-emailtoken)
14. [Get Authenticated User Profile (GET /{box_slug}/me)](#14-get-authenticated-user-profile-get-box_slugme)
15. [Logout (POST /{box_slug}/logout)](#15-logout-post-box_sluglogout)

---

## 1. Register Company/Tenant (POST `/register`)

Registers a new user, creates their tenant space (Box), assigns role `admin`, and schedules verification mail.

*   **URL**: `/register`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Request Body (JSON)**:
    ```json
    {
      "name": "John Doe",
      "email": "johndoe@example.com",
      "phone": "9876543210",
      "company_name": "ACME Tech Ltd",
      "industry": 1,
      "country_id": "101",
      "state_id": "22",
      "city_id": "2707",
      "pincode": "400001",
      "address": "123 Business Lane, Mumbai"
    }
    ```

*   **Success Response** (201 Created):
    ```json
    {
      "success": true,
      "message": "Registration completed successfully. Please check your email to verify your account.",
      "user": {
        "id": 12,
        "name": "John Doe",
        "email": "johndoe@example.com"
      },
      "box": {
        "id": 5,
        "name": "ACME Tech Ltd",
        "slug": "acme-tech-ltd",
        "domain": "acme-tech-ltd.mycrm.com"
      }
    }
    ```

*   **Error Responses**:
    *   `422 Unprocessable Entity` (Validation failure / Duplicate check / Company name taken)
        ```json
        {
          "success": false,
          "message": "Validation failed.",
          "errors": {
            "email": ["Already in use in `another box`"]
          }
        }
        ```

---

## 2. Login with Password (POST `/login`)

Authenticates using email and password and returns a Laravel Sanctum API Token.

*   **URL**: `/login`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Request Body (JSON)**:
    ```json
    {
      "email": "johndoe@example.com",
      "password": "secretpassword"
    }
    ```

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "message": "Logged in successfully.",
      "access_token": "1|abc123xyz...",
      "token_type": "Bearer",
      "user": {
        "id": 12,
        "name": "John Doe",
        "email": "johndoe@example.com",
        "role": "admin"
      },
      "box": {
        "id": 5,
        "name": "ACME Tech Ltd",
        "slug": "acme-tech-ltd",
        "domain": "acme-tech-ltd.mycrm.com"
      },
      "dashboard_url": "http://your-domain.com/acme-tech-ltd/dashboard",
      "web_login_url": "http://your-domain.com/login"
    }
    ```

*   **Error Responses**:
    *   `403 Forbidden` (Email unverified or account disabled):
        ```json
        {
          "success": false,
          "message": "Your Email is not yet verified. Please verify your email first.",
          "resend": true
        }
        ```
    *   `422 Unprocessable Entity` (Incorrect password or validation failures)

---

## 3. Search Country, State, City (GET `/search-country`)

Searches the available geographic master tables.

*   **URL**: `/search-country`
*   **Method**: `GET`
*   **Auth Required**: No
*   **Query Parameters**:
    *   `type`: `country`, `state`, or `city` (Required)
    *   `keyword`: Optional filter string
    *   `country_id`: Required if `type` is `state`
    *   `state_id`: Required if `type` is `city`

*   **Request Examples**:
    *   `/search-country?type=country&keyword=Ind`
    *   `/search-country?type=state&country_id=101`
    *   `/search-country?type=city&state_id=22`

*   **Success Response** (200 OK):
    ```json
    {
      "data": [
        {
          "id": 101,
          "name": "India"
        }
      ]
    }
    ```

---

## 4. Get Countries List (GET `/countries`)

Gets a list of all enabled countries, optionally filtered by name.

*   **URL**: `/countries`
*   **Method**: `GET`
*   **Auth Required**: No
*   **Query Parameters**:
    *   `keyword`: Optional filter string to search countries by name.

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 101,
          "name": "India"
        }
      ]
    }
    ```

---

## 5. Get States List (GET `/states` or `/countries/{country_id}/states`)

Gets a list of all enabled states within a specified country.

*   **URL**: `/states` or `/countries/{country_id}/states`
*   **Method**: `GET`
*   **Auth Required**: No
*   **Query Parameters / Route Parameters**:
    *   `country_id`: The ID of the country (Required via route parameter or query string if using `/states`)
    *   `keyword`: Optional filter string to search states by name.

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 22,
          "name": "Maharashtra"
        }
      ]
    }
    ```

---

## 6. Get Cities List (GET `/cities` or `/states/{state_id}/cities`)

Gets a list of all enabled cities within a specified state.

*   **URL**: `/cities` or `/states/{state_id}/cities`
*   **Method**: `GET`
*   **Auth Required**: No
*   **Query Parameters / Route Parameters**:
    *   `state_id`: The ID of the state (Required via route parameter or query string if using `/cities`)
    *   `keyword`: Optional filter string to search cities by name.

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 2707,
          "name": "Mumbai"
        }
      ]
    }
    ```

---

## 7. Get Master Industries (GET `/industries`)

Gets a list of all active master industries for tenant registration dropdowns.

*   **URL**: `/industries`
*   **Method**: `GET`
*   **Auth Required**: No

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 1,
          "name": "Technology"
        },
        {
          "id": 2,
          "name": "Healthcare"
        }
      ]
    }
    ```

---

## 8. Resend Verification Email (POST `/resend-verification-email`)

Resends the verification email containing the token link for a registered user.

*   **URL**: `/resend-verification-email`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Request Body (JSON)**:
    ```json
    {
      "email": "johndoe@example.com"
    }
    ```

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "message": "Check your email inbox to verify your email, then login."
    }
    ```

*   **Error Responses**:
    *   `409 Conflict` (Already verified):
        ```json
        {
          "success": false,
          "message": "Email is already verified."
        }
        ```

---

## 9. Send OTP (POST `/send-otp`)

Sends a 6-digit OTP code to the registered email address.

*   **URL**: `/send-otp`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Request Body (JSON)**:
    ```json
    {
      "user_input": "johndoe@example.com" 
    }
    ```
    *(Note: `user_input` accepts either registered email address or mobile number)*

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "message": "OTP sent successfully and valid for 10 minutes."
    }
    ```

---

## 10. Verify OTP (POST `/verify-otp`)

Verifies the 6-digit OTP code, checks shift/timing/IP restrictions, and returns a Sanctum API token.

*   **URL**: `/verify-otp`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Request Body (JSON)**:
    ```json
    {
      "user_input": "johndoe@example.com",
      "otp": "123456"
    }
    ```

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "message": "OTP verified successfully.",
      "access_token": "3|xyz987...",
      "token_type": "Bearer",
      "user": {
        "id": 12,
        "name": "John Doe",
        "email": "johndoe@example.com",
        "role": "admin"
      },
      "box": {
        "id": 5,
        "name": "ACME Tech Ltd",
        "slug": "acme-tech-ltd",
        "domain": "acme-tech-ltd.mycrm.com"
      },
      "dashboard_url": "http://your-domain.com/acme-tech-ltd/dashboard",
      "web_login_url": "http://your-domain.com/login"
    }
    ```

*   **Error Responses**:
    *   `410 Gone` (OTP expired)
    *   `422 Unprocessable Entity` (Incorrect OTP)
    *   `403 Forbidden` (Work mode/IP/Weekly off restrictions check failed)

---

## 11. Forgot Password Link Request (POST `/forget-email`)

Requests a reset password link via email if the account exists.

*   **URL**: `/forget-email`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Request Body (JSON)**:
    ```json
    {
      "email": "johndoe@example.com"
    }
    ```

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "message": "If your email exists in our system, you will receive a password reset link shortly."
    }
    ```

---

## 12. Submit Reset Password (POST `/reset-password-submit`)

Submits the new password using the token sent in the reset email.

*   **URL**: `/reset-password-submit`
*   **Method**: `POST`
*   **Auth Required**: No
*   **Request Body (JSON)**:
    ```json
    {
      "token": "reset_token_here",
      "password": "newsecretpassword",
      "password_confirmation": "newsecretpassword"
    }
    ```

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "message": "Password reset successful!"
    }
    ```

---

## 13. Verify Email Token (GET `/verify-email/{token}`)

Verifies the email address using the `remember_token` sent during registration.

*   **URL**: `/verify-email/{token}`
*   **Method**: `GET`
*   **Auth Required**: No

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "message": "Your account has been verified successfully."
    }
    ```

*   **Error Responses**:
    *   `422 Unprocessable Entity` (Invalid or expired token)

---

## 14. Get Authenticated User Profile (GET `/{box_slug}/me`)

Retrieves detailed tenant, employee, and user profile information.

*   **URL**: `/{box_slug}/me`
*   **Method**: `GET`
*   **Auth Required**: Yes (`Bearer <token>`)
*   **Headers**:
    ```http
    Authorization: Bearer <your_token>
    ```

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "user": {
        "id": 12,
        "name": "John Doe",
        "email": "johndoe@example.com",
        "mobile": "9876543210"
      },
      "box_user": {
        "id": 10,
        "employee_id": "AC-01",
        "staff_role": "admin",
        "status": "Enabled",
        "company": "ACME Tech Ltd",
        "box": {
          "id": 5,
          "name": "ACME Tech Ltd",
          "slug": "acme-tech-ltd",
          "domain": "acme-tech-ltd.mycrm.com"
        }
      }
    }
    ```

---

## 15. Logout (POST `/{box_slug}/logout`)

Deletes the token currently used for authentication.

*   **URL**: `/{box_slug}/logout`
*   **Method**: `POST`
*   **Auth Required**: Yes (`Bearer <token>`)
*   **Headers**:
    ```http
    Authorization: Bearer <your_token>
    ```

*   **Success Response** (200 OK):
    ```json
    {
      "success": true,
      "message": "Logged out successfully."
    }
    ```

