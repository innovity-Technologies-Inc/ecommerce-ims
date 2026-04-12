<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReturnReceiveRequest;
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

    public function receive(ReturnReceiveRequest $request, int $id): RedirectResponse
    {
        $this->returnService->receiveReturn($id, $request->validated());

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

    /**
     * Get batches for an order item.
     */
    public function getOrderBatches(Request $request): \Illuminate\Http\JsonResponse
    {
        $batches = $this->returnService->getOrderBatches($request->order_item_id);

        return response()->json($batches);
    }

    /**
     * Get serials for an order item and batch.
     */
    public function getOrderSerials(Request $request): \Illuminate\Http\JsonResponse
    {
        $serials = $this->returnService->getOrderSerials($request->order_item_id, $request->batch_id);

        return response()->json($serials);
    }
}
