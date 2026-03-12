<?php

namespace App\Services;

use App\Models\ContactMessage;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactService
{
    /**
     * Get all contact messages with search and sorting.
     */
    public function getAllMessages(array $params = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ContactMessage::query();

        // Apply Search using FlexSearch
        if (! empty($params['search'])) {
            $flexSearch = app(FlexSearch::class);
            $query = $flexSearch->apply($query, [], $params['search'], ['name', 'email', 'subject', 'message']);
        }

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a message by ID and mark it as read.
     */
    public function getMessageById(int $id): ContactMessage
    {
        $message = ContactMessage::findOrFail($id);

        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        return $message;
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
