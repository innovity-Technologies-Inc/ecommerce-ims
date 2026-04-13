<?php

namespace App\Services;

use App\Models\Faq;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FaqService
{
    /**
     * Get all FAQs with search and filtering.
     */
    public function getAllFaqs(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Faq::query();

        $filters = [];
        if (isset($params['status']) && $params['status'] !== '') {
            $filters['is_active'] = $params['status'];
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['question', 'answer'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        return $query->orderBy('sort_order', 'asc')->paginate($perPage);
    }

    /**
     * Store a newly created FAQ.
     */
    public function storeFaq(array $data): Faq
    {
        return Faq::create([
            'question' => $data['question'],
            'answer' => $data['answer'],
            'is_active' => isset($data['is_active']),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    /**
     * Update the specified FAQ.
     */
    public function updateFaq(Faq $faq, array $data): bool
    {
        return $faq->update([
            'question' => $data['question'],
            'answer' => $data['answer'],
            'is_active' => isset($data['is_active']),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    /**
     * Delete the specified FAQ.
     */
    public function deleteFaq(Faq $faq): bool
    {
        return $faq->delete();
    }

    /**
     * Get active FAQs for client side.
     */
    public function getActiveFaqs()
    {
        return Faq::where('is_active', true)->orderBy('sort_order', 'asc')->get();
    }
}
