<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct(protected ContactService $contactService) {}

    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        $messages = $this->contactService->getAllMessages($request->all());

        if ($request->ajax()) {
            return view('admin.contact_messages.partials.table', compact('messages'))->render();
        }

        return view('admin.contact_messages.index', compact('messages'));
    }

    /**
     * Display the specified contact message.
     */
    public function show(int $id): View
    {
        $message = $this->contactService->getMessageById($id);

        return view('admin.contact_messages.show', compact('message'));
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(int $id): JsonResponse
    {
        $this->contactService->markAsRead($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Message marked as read',
        ]);
    }

    /**
     * Toggle read status of a message.
     */
    public function toggleReadStatus(int $id): JsonResponse
    {
        $this->contactService->toggleReadStatus($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Message status updated successfully',
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
