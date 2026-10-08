<?php
namespace App\Http\Controllers;
use App\Models\{Box, GmailInbox, Lead, GmailMessage, Activity, BoxCrmIntegration};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Http, Log, Auth, Mail};
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function connect(Request $request, $boxSlug = null)
    {
        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'update', 'api_keys'))) {
            abort(403, 'You do not have permission to connect API integrations.');
        }

        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect') ?: route('google.callback');

        // Use route param if available, otherwise try input
        if (!$boxSlug) {
            $boxSlug = $request->input('box_slug', '');
        }

        $currentUrl = $request->input('current_url', url('/'));

        // Scopes for Gmail
        $scopes = [
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.send',
            'https://www.googleapis.com/auth/gmail.modify',
        ];

        $state = base64_encode(json_encode([
            'box_slug' => $boxSlug,
            'current_url' => $currentUrl,
        ]));

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => implode(' ', $scopes),
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ];

        $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($params);

        return redirect($url);
    }

    public function callback(Request $request)
    {
        $code = $request->input('code');
        $error = $request->input('error');
        $stateData = json_decode(base64_decode($request->input('state')), true);

        if ($error || !$code) {
            Log::error('Google OAuth Callback Error', ['error' => $error, 'code' => $code]);
            return redirect($stateData['current_url'] ?? '/')->with('error', 'Google authorization failed: ' . ($error ?? 'Unknown error'));
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect') ?: route('google.callback');

        // Exchange code for tokens
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if ($response->failed()) {
            Log::error('Google OAuth Token Exchange Failed', $response->json());
            return redirect($stateData['current_url'] ?? '/')->with('error', 'Failed to get tokens from Google.');
        }
        $tokens = $response->json();
        $userResponse = Http::withToken($tokens['access_token'])->get('https://www.googleapis.com/oauth2/v3/userinfo');
        if ($userResponse->failed()) {
            Log::error('Google UserInfo Fetch Failed', $userResponse->json());
            return redirect($stateData['current_url'] ?? '/')->with('error', 'Failed to get user info from Google.');
        }

        $userInfo = $userResponse->json();
        $email = $userInfo['email'];

        $boxSlug = $stateData['box_slug'] ?? null;
        $box = null;
        if ($boxSlug) {
            $box = Box::where('slug', $boxSlug)->first();
        }

        // Save or update the Gmail inbox
        $userId = Auth::id();
        if (!$userId) {
            Log::warning('Google OAuth Callback: User session lost during redirect.', [
                'email' => $email,
                'box' => $boxSlug,
                'current_url' => $stateData['current_url'] ?? 'n/a'
            ]);
            // If the user belongs to a specific box, we should ideally redirect them back to that box's login
            return redirect()->route('login')->with('error', 'Your session expired during Google authentication. Please login and try again.');
        }

        GmailInbox::updateOrCreate(
            ['email' => $email],
            [
                'box_id' => $box ? $box->id : null,
                'user_id' => $userId,
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'] ?? null,
                'expires_in' => $tokens['expires_in'] ?? null,
                'token_type' => $tokens['token_type'] ?? null,
                'status' => 'Enabled',
            ]
        );

        return redirect($stateData['current_url'] ?? '/')->with('success', 'Gmail connected successfully!');
    }

    public function syncEmails(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }

        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box context required.'], 400);
        }

        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'api_keys'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to sync emails.'], 403);
        }

        $inbox = GmailInbox::where('box_id', $box->id)->where('status', 'Enabled')->first();
        if (!$inbox) {
            return response()->json(['success' => false, 'message' => 'No enabled Gmail inbox found for this box.'], 404);
        }
        $this->refreshTokenIfExpired($inbox);
        try {
            $response = Http::withToken($inbox->access_token)
                ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages", [
                        'maxResults' => 100,
                        'q' => 'is:unread',
                    ]);

            if ($response->status() === 401) {
                if ($this->refreshToken($inbox)) {
                    $response = Http::withToken($inbox->access_token)
                        ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages", [
                        'maxResults' => 100,
                        'q' => 'is:unread',
                    ]);
                }
            }
            if ($response->failed()) {
                return response()->json(['success' => false, 'message' => 'Failed to fetch messages from Gmail.'], 500);
            }
            $messages = $response->json()['messages'] ?? [];
            $newCount = 0;
            $integration = BoxCrmIntegration::where('box_id', $box->id)
                ->whereHas('api', fn($q) => $q->where('system_name', 'gmail'))
                ->first();
            $filterData = [
                'round_robin' => $integration->params['round_robin'] ?? 0,
                'round_robin_users' => $integration->params['round_robin_users'] ?? [],
                'assigned_to' => $integration->params['assigned_to'] ?? $box->superadmin_id,
                'lead_source_id' => $integration->params['lead_source_id'] ?? null,
                'lead_status_id' => $integration->params['lead_status_id'] ?? null,
            ];

            $mapper = [
                'name' => 'name',
                'email' => 'email',
                'phone' => 'phone',
                'query' => 'query',
            ];

            foreach ($messages as $msg) {
                $existingMsg = GmailMessage::where('message_id', $msg['id'])->first();
                if ($existingMsg && $existingMsg->is_processed) {
                    // Mark as read in Gmail if it's already in our database and processed
                    try {
                        Http::withToken($inbox->access_token)
                            ->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}/modify", [
                            'removeLabelIds' => ['UNREAD']
                        ]);
                    }
                    catch (\Exception $e) {
                    }
                    continue;
                }

                if ($this->processEmail($inbox, $msg, $box, $filterData, $mapper)) {
                    $newCount++;
                }
            }

            $inbox->update(['last_sync_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => "Synced successfully. Found {$newCount} new messages.",
                'new_count' => $newCount
            ]);

        }
        catch (\Exception $e) {
            Log::error('Gmail Sync Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during sync: ' . $e->getMessage()], 500);
        }
    }

    public function refreshToken($inbox)
    {
        if (!$inbox->refresh_token) {
            return false;
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'refresh_token' => $inbox->refresh_token,
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $inbox->update([
                'access_token' => $data['access_token'],
                'expires_in' => $data['expires_in'] ?? $inbox->expires_in,
                'updated_at' => now(),
            ]);
            Log::info("Gmail token refreshed for: " . $inbox->email);
            return true;
        }

        Log::error("Gmail token refresh FAILED for: " . $inbox->email . " - " . $response->body());
        return false;
    }

    public function refreshTokenIfExpired($inbox)
    {
        $expiresAt = $inbox->updated_at->addSeconds(($inbox->expires_in ?? 3600) - 60);
        if ($expiresAt->isPast()) {
            return $this->refreshToken($inbox);
        }
        return false;
    }

    public function processEmail($inbox, $msg, $box, $filterData, $mapper)
    {
        $msgResponse = Http::withToken($inbox->access_token)
            ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}");
        if (!$msgResponse->successful()) {
            return false;
        }
        $fullMsg = $msgResponse->json();
        $headers = $fullMsg['payload']['headers'];

        // Use internalDate from Gmail (converted from milliseconds)
        $receivedAt = isset($fullMsg['internalDate'])
            ?\Carbon\Carbon::createFromTimestampMs($fullMsg['internalDate'])
            : now();

        $subject = collect($headers)->firstWhere('name', 'Subject');
        $subject = is_array($subject) ? ($subject['value'] ?? '(No Subject)') : ($subject->value ?? '(No Subject)');

        $from = collect($headers)->firstWhere('name', 'From');
        $from = is_array($from) ? ($from['value'] ?? '') : ($from->value ?? '');

        $fromName = $from;
        $fromEmail = $from;
        if (preg_match('/(.*)<(.*)>/', $from, $matches)) {
            $fromName = trim($matches[1], ' "');
            $fromEmail = trim($matches[2]);
        }
        $fromEmail = trim($fromEmail);

        $to = collect($headers)->firstWhere('name', 'To');
        $to = is_array($to) ? ($to['value'] ?? '') : ($to->value ?? '');
        $toEmail = $to;
        if (preg_match('/(.*)<(.*)>/', $to, $matches)) {
            $toEmail = trim($matches[2]);
        }
        $toEmail = trim($toEmail);

        $bodyData = $this->getGmailBody($fullMsg['payload']);
        $bodyText = $bodyData['text'] ?? '';
        $bodyHtml = $bodyData['html'] ?? '';

        $gmailMsg = GmailMessage::updateOrCreate(
        ['message_id' => $fullMsg['id']],
        [
            'gmail_inbox_id' => $inbox->id,
            'thread_id' => $fullMsg['threadId'],
            'from_email' => $fromEmail,
            'from_name' => $this->cleanString($fromName),
            'subject' => $this->cleanString($subject),
            'snippet' => $this->cleanString($fullMsg['snippet']),
            'body' => $bodyHtml,
            'received_at' => $receivedAt,
            'raw_data' => $fullMsg,
        ]
        );

        // Mark as read in Gmail as soon as it's fetched into CRM database
        try {
            Http::withToken($inbox->access_token)
                ->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$fullMsg['id']}/modify", [
                'removeLabelIds' => ['UNREAD']
            ]);
        }
        catch (\Exception $e) {
            Log::error("Failed to mark Gmail message {$fullMsg['id']} as read: " . $e->getMessage());
        }

        if ($gmailMsg->is_processed) {
            return false;
        }

        $lead = null;

        // First, try to match by thread_id to keep conversations grouped to the same lead
        $existingThreadMsg = \App\Models\GmailMessage::where('thread_id', $fullMsg['threadId'])
            ->whereNotNull('lead_id')
            ->first();

        if ($existingThreadMsg) {
            $lead = \App\Models\Lead::find($existingThreadMsg->lead_id);
        }

        if (!$lead) {
            $isSent = in_array('SENT', $fullMsg['labelIds'] ?? []);
            $searchEmail = $isSent ? $toEmail : $fromEmail;
            
            $lead = \App\Models\Lead::where('box_id', $box->id)
                ->where(function ($q) use ($searchEmail) {
                    $q->where('email', $searchEmail)
                      ->orWhere('sec_email', $searchEmail);
                })
                ->first();

            // Fallback
            if (!$lead) {
                $lead = \App\Models\Lead::where('box_id', $box->id)
                    ->where(function ($q) use ($fromEmail, $toEmail) {
                    $q->where('email', $fromEmail)
                        ->orWhere('sec_email', $fromEmail);
                    if (!empty($toEmail)) {
                        $q->orWhere('email', $toEmail)
                          ->orWhere('sec_email', $toEmail);
                    }
                })
                    ->first();
            }
        }

        if ($lead) {
            $activityType = \App\Models\ActivityType::where('box_id', $box->id)
                ->where('activity_type', 'like', '%email%')
                ->first();

            $isSent = in_array('SENT', $fullMsg['labelIds'] ?? []);
            $activitySubject = $isSent ? 'Email Sent: ' . $subject : 'Email Received: ' . $subject;
            $activityDescription = $isSent 
                ? "Sent an email to {$toEmail}.\n\nContent:\n" . \Illuminate\Support\Str::limit(strip_tags($bodyText), 1000)
                : "Received an email from {$fromEmail}.\n\nContent:\n" . \Illuminate\Support\Str::limit(strip_tags($bodyText), 1000);

            $activity = \App\Models\Activity::create([
                'box_id' => $box->id,
                'activity_id' => $lead->id,
                'activity_type' => $activityType ? $activityType->id : null,
                'assigned_to' => $lead->assigned_to ?? ($inbox->user_id ?? $box->superadmin_id),
                'type' => 'email',
                'subject' => $activitySubject,
                'description' => $activityDescription,
                'params' => json_encode(['gmail_message_id' => $gmailMsg->id]),
                'activity_date' => $receivedAt,
                'added_by' => $inbox->user_id ?? $box->superadmin_id,
                'last_updated_by' => $inbox->user_id ?? $box->superadmin_id,
                'status' => 'Close',
                'completed_date' => now(),
                'created_from' => 'Gmail'
            ]);
            
            if (!$isSent && $lead->assigned_to) {
                // Incoming email, notify assigned user
                \App\Jobs\SendTaskNotificationJob::dispatch($activity->id, $lead->assigned_to);
            }
            $gmailMsg->update([
                'lead_id' => $lead->id,
                'is_processed' => true,
                'activity_status' => 'Uploaded'
            ]);
            return true;
        }

        // If no matching lead found, mark as processed with 'Skip' status
        $gmailMsg->update([
            'is_processed' => true,
            'activity_status' => 'Skip'
        ]);
        return false;
    }

    private function getGmailBody($payload)
    {
        $body = "";
        $rawHtml = "";
        if (isset($payload['parts'])) {
            $plainText = "";
            $htmlText = "";
            foreach ($payload['parts'] as $part) {
                if ($part['mimeType'] == 'text/plain' && isset($part['body']['data'])) {
                    $plainText .= base64_decode(str_replace(['-', '_'], ['+', '/'], $part['body']['data']));
                }
                elseif ($part['mimeType'] == 'text/html' && isset($part['body']['data'])) {
                    $htmlText .= base64_decode(str_replace(['-', '_'], ['+', '/'], $part['body']['data']));
                }
                elseif (isset($part['parts'])) {
                    $nested = $this->getGmailBody($part);
                    $htmlText .= $nested['html'] ?? '';
                    $plainText .= $nested['text'] ?? '';
                }
            }
            $rawHtml = $htmlText;
            if (!empty(trim($htmlText))) {
                $cleanText = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $htmlText);
                $cleanText = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cleanText);
                $body = strip_tags($cleanText, '<img><br><p><a><div><span><b><i><u>');
            }
            else {
                $body = nl2br(e($plainText));
            }
        }
        elseif (isset($payload['body']['data'])) {
            $data = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload['body']['data']));
            if (isset($payload['mimeType']) && $payload['mimeType'] == 'text/html') {
                $rawHtml = $data;
                $cleanText = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $data);
                $cleanText = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cleanText);
                $body = strip_tags($cleanText, '<img><br><p><a><div><span><b><i><u>');
            }
            else {
                $rawHtml = nl2br(e($data));
                $body = $rawHtml;
            }
        }

        $imagePattern = '/(?<!src=")(?<!href=")(https?:\/\/[^\s]+?\.(?:jpg|jpeg|gif|png|webp|svg)(?:\?[^\s]*)?)/i';
        $imageReplacement = '<div class="email-image-wrapper" style="margin:10px 0;"><img src="$1" style="max-width:100%; height:auto; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);"></div>';
        $body = preg_replace($imagePattern, $imageReplacement, $body);

        // Robust cleaning for database compatibility
        $body = mb_convert_encoding($body, 'UTF-8', 'UTF-8');
        $body = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $body);

        if (empty(trim($rawHtml))) {
            $rawHtml = $body;
        }
        $rawHtml = mb_convert_encoding($rawHtml, 'UTF-8', 'UTF-8');
        $rawHtml = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $rawHtml);

        return [
            'text' => trim($body),
            'html' => trim($rawHtml)
        ];
    }

    public function disconnect(Request $request, $box_slug, $id)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found.'], 404);
        }

        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'update', 'api_keys'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to disconnect integrations.'], 403);
        }

        $inbox = GmailInbox::where('id', $id)->where('box_id', $box->id)->first();
        if (!$inbox) {
            return response()->json(['success' => false, 'message' => 'Inbox not found or already disconnected.'], 404);
        }

        $email = $inbox->email;
        $token = $inbox->refresh_token ?: $inbox->access_token;

        // 1. Revoke token from Google
        if ($token) {
            try {
                $response = Http::asForm()->post('https://oauth2.googleapis.com/revoke', [
                    'token' => $token
                ]);

                if ($response->successful()) {
                    Log::info("Google token revoked successfully for: " . $email);
                }
                else {
                    Log::warning("Google token revocation returned non-success for: " . $email . " - " . $response->body());
                }
            }
            catch (\Exception $e) {
                Log::error("Error revoking Google token for {$email}: " . $e->getMessage());
            }
        }

        // 2. Send Notification Email
        try {
            Mail::raw("Hello,\n\nYour Gmail account ($email) has been disconnected from " . ($box->name ?? 'Bizz CRM') . ".\n\nYou will no longer receive leads or sync messages from this account.\n\nRegards,\nTeam Bizz CRM", function ($message) use ($email, $box) {
                $message->to($email)
                    ->subject('Gmail Disconnected - ' . ($box->name ?? 'Bizz CRM'));
            });
        }
        catch (\Exception $e) {
            Log::error("Failed to send disconnection email to {$email}: " . $e->getMessage());
        }

        // 3. Delete the record
        $inbox->delete();

        return response()->json(['success' => true, 'message' => "Gmail account {$email} disconnected and access revoked successfully."]);
    }

    public function downloadAttachment($box_slug, $message_id, $attachment_id, $filename)
    {
        $userId = Auth::id();
        $inbox = GmailInbox::where('user_id', $userId)->first();
        if (!$inbox) {
            $box = Box::where('slug', $box_slug)->first();
            $inbox = GmailInbox::where('box_id', $box->id ?? 0)->first();
        }

        if (!$inbox) {
            abort(404, 'Gmail integration not found for this user/box');
        }

        $this->refreshTokenIfExpired($inbox);

        $response = Http::withToken($inbox->access_token)
            ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$message_id}/attachments/{$attachment_id}");

        if (!$response->successful()) {
            Log::error("Gmail Attachment Fetch Error: " . $response->body());
            abort(500, 'Failed to fetch attachment from Gmail');
        }

        $data = $response->json();
        if (!isset($data['data'])) {
            abort(404, 'Attachment data missing from Gmail response');
        }

        // Gmail API returns base64url encoded data
        $decodedData = base64_decode(strtr($data['data'], '-_', '+/'));

        return response($decodedData)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function cleanString($string)
    {
        if (empty($string))
            return $string;
        // Strip invalid UTF-8 and control characters
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $string);
    }
    public function sendEmail(\Illuminate\Http\Request $request, $box_slug)
    {
        try {
            $box = \App\Models\Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json(["success" => false, "message" => "Box not found"], 404);
            }
            $boxId = $box->id;
            $userId = \Illuminate\Support\Facades\Auth::id();
            
            $request->validate([
                "lead_id" => "required|integer",
                "to_email" => "required|email",
                "subject" => "required|string|max:255",
                "body" => "required|string",
                "from_email_id" => "required|integer",
            ]);
            
            $inbox = \App\Models\GmailInbox::where("box_id", $boxId)
                ->where("user_id", $userId)
                ->where("status", "Enabled")
                ->where("id", $request->from_email_id)
                ->first();
            
            if (!$inbox) {
                return response()->json(["success" => false, "message" => "No connected Gmail account found."], 400);
            }
            
            $this->refreshTokenIfExpired($inbox);
            
            $to = $request->to_email;
            $subject = $request->subject;
            $body = $request->body;
            $from = $inbox->email;
            
            $rawMessageString = "From: $from\r\n";
            $rawMessageString .= "To: $to\r\n";
            $rawMessageString .= "Subject: =?utf-8?B?".base64_encode($subject)."?=\r\n";
            $rawMessageString .= "MIME-Version: 1.0\r\n";
            
            if ($request->hasFile('attachments')) {
                $boundary = uniqid('np');
                $rawMessageString .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";
                
                // Text part
                $rawMessageString .= "--$boundary\r\n";
                $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
                $rawMessageString .= $body . "\r\n\r\n";

                // Attachments
                foreach ($request->file('attachments') as $file) {
                    $filename = $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $fileContent = base64_encode(file_get_contents($file->getRealPath()));
                    $fileContent = chunk_split($fileContent);

                    $rawMessageString .= "--$boundary\r\n";
                    $rawMessageString .= "Content-Type: $mimeType; name=\"$filename\"\r\n";
                    $rawMessageString .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
                    $rawMessageString .= "Content-Transfer-Encoding: base64\r\n\r\n";
                    $rawMessageString .= $fileContent . "\r\n\r\n";
                }
                
                $rawMessageString .= "--$boundary--";
            } else {
                $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
                $rawMessageString .= $body;
            }
            
            $rawMessage = rtrim(strtr(base64_encode($rawMessageString), "+/", "-_"), "=");
            
            $response = \Illuminate\Support\Facades\Http::withToken($inbox->access_token)
                ->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/send", [
                    "raw" => $rawMessage
                ]);
            
            if ($response->successful()) {
                $responseData = $response->json();
                $messageId = $responseData['id'] ?? null;
                $threadId = $responseData['threadId'] ?? null;
                $gmailMsgId = null;

                $lead = \App\Models\Lead::find($request->lead_id);
                
                if ($messageId && $lead) {
                    $gmailMsg = \App\Models\GmailMessage::create([
                        'gmail_inbox_id' => $inbox->id,
                        'message_id' => $messageId,
                        'thread_id' => $threadId,
                        'from_email' => $from,
                        'from_name' => $inbox->name ?? $from,
                        'subject' => $subject,
                        'snippet' => \Illuminate\Support\Str::limit(strip_tags($body), 100),
                        'body' => $body,
                        'received_at' => now(),
                        'lead_id' => $lead->id,
                        'is_processed' => true,
                        'activity_status' => 'Uploaded',
                        'raw_data' => $responseData,
                    ]);
                    $gmailMsgId = $gmailMsg->id;
                }

                if ($lead) {
                    $activityType = \App\Models\ActivityType::where("box_id", $boxId)
                        ->where("activity_type", "like", "%email%")
                        ->first();
                    
                    $isReply = str_starts_with(trim($subject), 'Re:');
                    $userName = \Illuminate\Support\Facades\Auth::user()->name ?? 'User';
                    $activitySubject = $isReply ? $userName . ' Replied Back: ' . trim(substr(trim($subject), 3)) : $userName . ' Sent: ' . $subject;
                    $activityDescription = $isReply ? "Replied back to {$to}.\n\nContent:\n" . \Illuminate\Support\Str::limit(strip_tags($body), 1000) : "Sent an email to {$to}.\n\nContent:\n" . \Illuminate\Support\Str::limit(strip_tags($body), 1000);

                    $params = [];
                    if ($request->hasFile('attachments')) {
                        $files = [];
                        foreach ($request->file('attachments') as $file) {
                            $filename = $file->getClientOriginalName();
                            $path = $file->storeAs('activity_files/' . $box_slug . '/' . date('Y-m-d'), uniqid() . '_' . $filename, 'public');
                            $files[] = [
                                'original_name' => $filename,
                                'file_path' => $path,
                                'file_size' => $file->getSize()
                            ];
                        }
                        $params['files'] = $files;
                    }
                    
                    if ($gmailMsgId) {
                        $params['gmail_message_id'] = $gmailMsgId;
                    }

                    \App\Models\Activity::create([
                        'box_id' => $boxId,
                        'activity_id' => $lead->id,
                        'activity_type' => $activityType ? $activityType->id : null,
                        'assigned_to' => $lead->assigned_to ?? $userId,
                        'type' => 'email',
                        'subject' => $activitySubject,
                        'description' => $activityDescription,
                        'params' => empty($params) ? null : json_encode($params),
                        'activity_date' => now(),
                        'added_by' => $userId,
                        'last_updated_by' => $userId,
                        'status' => 'Close',
                        'completed_date' => now(),
                        'created_from' => 'CRM'
                    ]);
                }

                if ($request->has('create_followup') && $request->create_followup == 1) {
                    $followupTypeObj = \App\Models\ActivityType::where('box_id', $boxId)
                        ->where('activity_type', 'like', '%' . $request->followup_type . '%')
                        ->first();

                    $assignedToId = $request->followup_assigned_to ?? $userId;
                    /*
                     * NOTE: Subject generation is disabled here to save database storage space.
                     * The subject is now dynamically constructed on the frontend instead.
                     * DO NOT REMOVE THIS COMMENTED CODE (kept for reference, AI/developers must not delete).
                     *
                    $assignedName = ($assignedToId == $userId) ? 'Self' : (\App\Models\User::find($assignedToId)->name ?? 'Unknown');
                    $fuType = ucfirst($request->followup_type);
                    $userName = \Illuminate\Support\Facades\Auth::user()->name;
                    
                    $fuSubject = "{$userName} created a Follow up {$fuType} from sent email and assigned to {$assignedName}";
                    */
                    \App\Models\Activity::create([
                        'box_id'          => $boxId,
                        'activity_id'     => $lead->id,
                        'activity_type'   => $followupTypeObj ? $followupTypeObj->id : null,
                        'type'            => $request->followup_type,
                        'subject'         => null,
                        'description'     => $request->followup_remarks,
                        'activity_date'   => $request->followup_date,
                        'assigned_to'     => $assignedToId,
                        'added_by'        => $userId,
                        'last_updated_by' => $userId,
                        'status'          => 'Open',
                        'created_from'    => 'CRM',
                    ]);
                }

                return response()->json(["success" => true, "message" => "Email sent successfully."]);
            } else {
                \Illuminate\Support\Facades\Log::error("Failed to send email via Gmail API: " . $response->body());
                return response()->json(["success" => false, "message" => "Failed to send email: " . $response->json("error.message", "Unknown error")], 500);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Send Email Error: " . $e->getMessage());
            return response()->json(["success" => false, "message" => "An error occurred while sending the email."], 500);
        }
    }

    public function viewEmailHtml($box_slug, $id)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            abort(404, 'Box not found');
        }

        $gmailMsg = GmailMessage::where('id', $id)
            ->whereHas('inbox', function($q) use ($box) {
                $q->where('box_id', $box->id);
            })
            ->first();

        if (!$gmailMsg) {
            abort(404, 'Message not found');
        }

        return response($gmailMsg->body)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
