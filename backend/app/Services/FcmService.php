<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class FcmService
{
    protected $serviceAccountPath;

    public function __construct()
    {
        // Path to the service account JSON file
        $this->serviceAccountPath = storage_path('app/firebase-service-account.json');
    }

    public function sendPush($userId, $title, $body, $data = [])
    {
        if (!file_exists($this->serviceAccountPath)) {
            \Log::error('FCM Service Account file missing at ' . $this->serviceAccountPath);
            return false;
        }

        // Get tokens for the user
        $tokens = DB::table('device_registers')
            ->where('created_by', $userId)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token');

        if ($tokens->isEmpty()) {
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return false;
        }

        $serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
        $projectId = $serviceAccount['project_id'];

        // Ensure all data values are strings
        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[(string)$key] = (string)$value;
        }

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => (string)$title,
                        'body' => (string)$body,
                    ],
                    'data' => $stringData,
                    'android' => [
                        'notification' => [
                            'sound' => 'default'
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default'
                            ]
                        ]
                    ]
                ]
            ];

            \Log::info("FCM Sending Push to {$token} with payload: " . json_encode($payload));

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

            if ($response->failed()) {
                $error = $response->json();
                $errorCode = $error['error']['details'][0]['errorCode'] ?? $error['error']['status'] ?? '';
                
                if ($errorCode === 'UNREGISTERED' || $errorCode === 'INVALID_ARGUMENT') {
                    // Token is invalid, remove it
                    DB::table('device_registers')->where('fcm_token', $token)->delete();
                }
                
                \Log::error("FCM Send Error: " . json_encode($error));
            } else {
                \Log::info("FCM Send Success to token {$token}");
            }
        }

        return true;
    }

    protected function getAccessToken()
    {
        $serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);

        $clientEmail = $serviceAccount['client_email'];
        $privateKey = $serviceAccount['private_key'];

        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        
        $now = time();
        $claim = json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlClaim = $this->base64UrlEncode($claim);

        $signatureInput = $base64UrlHeader . '.' . $base64UrlClaim;
        
        $signature = '';
        openssl_sign($signatureInput, $signature, $privateKey, 'SHA256');
        
        $jwt = $signatureInput . '.' . $this->base64UrlEncode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        \Log::error("FCM Token Gen Error: " . $response->body());
        return null;
    }

    protected function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
