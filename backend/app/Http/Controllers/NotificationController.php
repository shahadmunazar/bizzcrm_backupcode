<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        $typeParam = $request->input('type');

        // If no type filter is provided, return latest unread notifications.
        if (!$typeParam) {
            return response()->json([
                'success' => true,
                'data' => $user->unreadNotifications()
                    ->latest()
                    ->limit(100)
                    ->get()
            ]);
        }

        $typeMap = [
            'task' => 'App\Notifications\TaskAssignedNotification',
            'lead' => ['App\Notifications\NewLeadNotification', 'App\Notifications\LeadActivityNotification'],
            'export_ready' => 'App\Notifications\ExportReadyNotification',
            'missed_punch_out' => 'App\Notifications\MissedPunchOutNotification',
            'general' => 'App\Notifications\GeneralNotification',
        ];

        $requestedTypes = collect(explode(',', $typeParam))
            ->map(fn ($type) => trim($type))
            ->filter()
            ->unique()
            ->values();

        /*
         * ---------------------------------------------------------
         * Laravel Notifications
         * ---------------------------------------------------------
         */
        $notificationClasses = $requestedTypes
            ->map(fn ($type) => $typeMap[$type] ?? null)
            ->flatten()
            ->filter()
            ->values();

        $notifications = collect();

        if ($notificationClasses->isNotEmpty()) {
            $notifications = $user->unreadNotifications()
                ->whereIn('type', $notificationClasses->all())
                ->latest()
                ->limit(100)
                ->get();
        }

        /*
         * ---------------------------------------------------------
         * WhatsApp Notifications
         * ---------------------------------------------------------
         */
        if ($requestedTypes->contains('whatsapp')) {
            $boxSlug = $request->route('box_slug');

            if ($boxSlug) {
                $boxId = \App\Models\Box::where('slug', $boxSlug)->value('id');

                if ($boxId) {
                    $whatsappNotifications = \App\Models\WhatsAppMessage::query()
                        ->where('box_id', $boxId)
                        ->where('is_read', false)
                        ->where('type', 'INCOMING')
                        ->latest('id')
                        ->limit(50)
                        ->get([
                            'id',
                            'text',
                            'sender_id',
                            'lead_id',
                            'created_at',
                        ])
                        ->map(function ($msg) {
                            return [
                                'id' => $msg->id,
                                'type' => 'App\Notifications\WhatsAppMessageNotification',
                                'data' => [
                                    'message' => $msg->text,
                                    'sender' => $msg->sender_id,
                                    'lead_id' => $msg->lead_id,
                                ],
                                'created_at' => $msg->created_at->toIso8601String(),
                                'read_at' => null,
                            ];
                        });

                    $notifications = $notifications
                        ->concat($whatsappNotifications);
                }
            }
        }

        /*
         * ---------------------------------------------------------
         * Sort all notification types together
         * ---------------------------------------------------------
         */
        $notifications = $notifications
            ->sortByDesc(function ($notification) {
                $createdAt = $notification['created_at']
                    ?? $notification->created_at
                    ?? null;

                return $createdAt ? strtotime($createdAt) : 0;
            })
            ->values()
            ->take(100);

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    public function markAsRead($box_slug, $id, Request $request)
    {
        $type = $request->input('type');
        \Illuminate\Support\Facades\Log::info("Marking as read: ID={$id} TYPE={$type} BOX={$box_slug}");
        
        if ($type == 'whatsapp') {
            $msg = \App\Models\WhatsAppMessage::find($id);
            if ($msg) {
                $msg->update(['is_read' => true]);
                \Illuminate\Support\Facades\Log::info("WhatsApp message updated successfully: {$id}");
            } else {
                \Illuminate\Support\Facades\Log::warning("WhatsApp message NOT FOUND: {$id}");
            }
            return response()->json(['success' => true]);
        }

        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $type = $request->input('type');
        
        if ($type == 'whatsapp') {
            $boxSlug = $request->route('box_slug') ?? 'default';
            $box = \App\Models\Box::where('slug', $boxSlug)->first();
            if ($box) {
                \App\Models\WhatsAppMessage::where('box_id', $box->id)
                    ->where('is_read', false)
                    ->where('type', 'INCOMING')
                    ->update(['is_read' => true]);
            }
            return response()->json(['success' => true]);
        }

        $unread = Auth::user()->unreadNotifications();
        
        if ($type == 'task') {
            $unread->whereIn('type', [
                'App\Notifications\TaskAssignedNotification',
                'App\Notifications\ExportReadyNotification',
                'App\Notifications\MissedPunchOutNotification',
                'App\Notifications\GeneralNotification'
            ])->update(['read_at' => now()]);
        } elseif ($type == 'lead') {
            $unread->whereIn('type', ['App\Notifications\NewLeadNotification', 'App\Notifications\LeadActivityNotification'])->update(['read_at' => now()]);
        } else {
            $unread->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function sendOverallNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $title = $request->input('title');
        $message = $request->input('message');
        
        // Find users to notify (all users in the same box)
        $boxSlug = $request->route('box_slug');
        if ($boxSlug) {
            $box = \App\Models\Box::where('slug', $boxSlug)->first();
            if ($box) {
                // Get users attached to this box
                $users = \App\Models\User::whereHas('boxes', function ($q) use ($box) {
                    $q->where('box_id', $box->id);
                })->get();
                
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\GeneralNotification($title, $message));
                return redirect()->route('admin.dashboard', ['box_slug' => $boxSlug])->with('success', 'Overall Notification sent successfully to all users.');
            }
        }
        
        return redirect()->route('admin.dashboard', ['box_slug' => $boxSlug ?? 'default'])->with('error', 'Failed to send notification.');
    }
}
