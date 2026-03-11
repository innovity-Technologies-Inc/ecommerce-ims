<?php

namespace App\Services;

use App\Mail\ContactConfirmationMail;
use App\Models\ContactMessage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    /**
     * Store a new contact message and send confirmation email.
     */
    public function storeMessage(array $data): ContactMessage
    {
        $message = ContactMessage::create($data);

        try {
            Mail::to($message->email)->send(new ContactConfirmationMail($message));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Illuminate\Support\Facades\Log::error('Contact Confirmation Email failed: '.$e->getMessage());
        }

        return $message;
    }

    /**
     * Get all contact messages for admin listing.
     */
    public function getAllMessages(): LengthAwarePaginator
    {
        return ContactMessage::latest()->paginate(15);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(int $id): bool
    {
        $message = ContactMessage::findOrFail($id);

        return $message->update(['is_read' => true]);
    }

    /**
     * Delete a contact message.
     */
    public function deleteMessage(int $id): bool
    {
        $message = ContactMessage::findOrFail($id);

        return $message->delete();
    }
}
