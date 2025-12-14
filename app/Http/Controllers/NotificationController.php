<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = $request->integer('per_page', 10);

        $query = Notification::where('recipient_id', $user->id)->orderByDesc('created_at');

        $items = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
            'unread_count' => Notification::where('recipient_id', $user->id)->where('is_read', false)->count(),
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $user = $request->user();
        $notif = Notification::where('recipient_id', $user->id)->where('id', $id)->first();
        if (!$notif) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notif->is_read = true;
        $notif->read_at = now();
        $notif->save();

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        Notification::where('recipient_id', $user->id)->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
