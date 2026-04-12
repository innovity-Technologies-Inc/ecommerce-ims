<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    /**
     * Display a listing of notifications.
     */
    public function index(Request $request): View|\Illuminate\Http\Response
    {
        $notifications = $this->notificationService->getAdminNotifications($request->all());

        if ($request->ajax()) {
            return response()->view('admin.notifications.partials.table', compact('notifications'));
        }

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read and redirect to target URL.
     */
    public function markAsRead(int $id): RedirectResponse
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->update(['is_read' => true]);

        if ($notification->url) {
            return redirect($notification->url);
        }

        return redirect()->back()->with([
            'message' => 'Notification marked as read.',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        AdminNotification::unread()->update(['is_read' => true]);

        return redirect()->back()->with([
            'message' => 'All notifications marked as read.',
            'alert-type' => 'success',
        ]);
    }
}
