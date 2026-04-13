<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use App\Services\FaqService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct(protected FaqService $faqService) {}

    /**
     * Display a listing of the FAQs.
     */
    public function index(Request $request)
    {
        $faqs = $this->faqService->getAllFaqs($request->all());

        if ($request->ajax()) {
            return view('admin.faqs.partials.table', compact('faqs'))->render();
        }

        return view('admin.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create(): View
    {
        return view('admin.faqs.form');
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(FaqRequest $request): RedirectResponse
    {
        $this->faqService->storeFaq($request->validated());

        return redirect()->route('admin.faqs.index')->with([
            'message' => 'FAQ created successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit(Faq $faq): View
    {
        return view('admin.faqs.form', compact('faq'));
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(Faq $faq, FaqRequest $request): RedirectResponse
    {
        $this->faqService->updateFaq($faq, $request->validated());

        return redirect()->route('admin.faqs.index')->with([
            'message' => 'FAQ updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $this->faqService->deleteFaq($faq);

        return redirect()->route('admin.faqs.index')->with([
            'message' => 'FAQ deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
