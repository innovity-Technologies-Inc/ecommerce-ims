<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReturnRequestStatusUpdateRequest;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function __construct(protected ReturnService $returnService) {}

    public function requests(Request $request): View
    {
        $requests = $this->returnService->getReturnRequests($request->all());

        return view('admin.returns.requests', compact('requests'));
    }

    public function showRequest(int $id): View
    {
        $request = $this->returnService->getReturnRequestDetails($id);

        return view('admin.returns.show_request', compact('request'));
    }

    public function updateStatus(ReturnRequestStatusUpdateRequest $request, int $id): RedirectResponse
    {
        $this->returnService->updateStatus($id, $request->validated());

        return redirect()->back()->with('success', 'Return request status updated successfully.');
    }

    public function receive(int $id): RedirectResponse
    {
        $this->returnService->receiveReturn($id);

        return redirect()->back()->with('success', 'Return received and processed successfully.');
    }

    public function returnedProducts(Request $request): View
    {
        $items = $this->returnService->getReturnedProducts($request->all());

        return view('admin.returns.returned_products', compact('items'));
    }

    public function wastages(Request $request): View
    {
        $wastages = $this->returnService->getWastages($request->all());

        return view('admin.returns.wastages', compact('wastages'));
    }
}
