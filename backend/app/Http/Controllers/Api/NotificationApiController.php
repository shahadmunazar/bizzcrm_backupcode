<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $notifications = $request->user()->notifications()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'success' => true,
            'count' => $request->user()->unreadNotifications()->count()
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
        }

        return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
    }

    public function dashboardNotifications(Request $request)
    {
        // For employee dashboard card - maybe latest 5 unread
        $notifications = $request->user()->notifications()->take(5)->get();
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }
}
