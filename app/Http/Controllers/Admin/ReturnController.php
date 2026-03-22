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

    public function requests(Request $request): View|\Illuminate\Http\Response
    {
        $requests = $this->returnService->getReturnRequests($request->all());

        if ($request->ajax()) {
            return response()->view('admin.returns.partials.requests_table', compact('requests'));
        }

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

    public function returnedProducts(Request $request): View|\Illuminate\Http\Response
    {
        $items = $this->returnService->getReturnedProducts($request->all());

        if ($request->ajax()) {
            return response()->view('admin.returns.partials.returned_products_table', compact('items'));
        }

        return view('admin.returns.returned_products', compact('items'));
    }

    public function wastages(Request $request): View|\Illuminate\Http\Response
    {
        $wastages = $this->returnService->getWastages($request->all());

        if ($request->ajax()) {
            return response()->view('admin.returns.partials.wastages_table', compact('wastages'));
        }

        return view('admin.returns.wastages', compact('wastages'));
    }
}
