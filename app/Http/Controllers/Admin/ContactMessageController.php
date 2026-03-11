<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct(protected ContactService $contactService) {}

    /**
     * Display a listing of the contact messages.
     */
    public function index(): View
    {
        $messages = $this->contactService->getAllMessages();

        return view('admin.contact_messages.index', compact('messages'));
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(int $id): RedirectResponse
    {
        $this->contactService->markAsRead($id);

        return back()->with([
            'message' => 'Message marked as read',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->contactService->deleteMessage($id);

        return back()->with([
            'message' => 'Message deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
