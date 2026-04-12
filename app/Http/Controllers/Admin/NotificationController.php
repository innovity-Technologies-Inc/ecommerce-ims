<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(Request $request): View
    {
        $query = AdminNotification::query();

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Type Filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $query->latest()->paginate(20);

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
