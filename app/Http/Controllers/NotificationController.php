<?php

namespace App\Http\Controllers;

use App\Models\User; // ← Ajouter
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $notifications = $user->notifications()->paginate(20);
        $user->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(string $id)
    {
        /** @var User $user */
        $user = Auth::user();

        $notif = $user->notifications()->find($id);
        if ($notif) $notif->markAsRead();

        return response()->json(['ok' => true]);
    }

    public function markAllAsRead()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return back();
    }
}
