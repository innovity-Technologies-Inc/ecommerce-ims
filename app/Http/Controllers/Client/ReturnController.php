<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ReturnRequestStoreRequest;
use App\Services\ReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function __construct(protected ReturnService $returnService) {}

    public function index(): View
    {
        return view('client.returns.index');
    }

    public function getOrderDetails(Request $request): JsonResponse
    {
        $orderId = $request->query('order_id');
        $order = $this->returnService->getOrderDetails($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if ($this->returnService->checkExistingReturn($order->id)) {
            return response()->json(['message' => 'A return request has already been submitted for this order.'], 400);
        }

        return response()->json([
            'order' => $order,
            'html' => view('client.returns.partials.order_items', compact('order'))->render()
        ]);
    }

    public function store(ReturnRequestStoreRequest $request): JsonResponse
    {
        if ($this->returnService->checkExistingReturn($request->order_id_pk)) {
            return response()->json(['message' => 'A return request has already been submitted for this order.'], 400);
        }

        $this->returnService->storeReturnRequest($request->validated());

        return response()->json(['message' => 'Return request submitted successfully.']);
    }

    public function track(Request $request): JsonResponse
    {
        $orderId = $request->query('order_id');
        $returnRequest = $this->returnService->trackReturn($orderId);

        if (! $returnRequest) {
            return response()->json(['message' => 'No return request found for this order.'], 404);
        }

        return response()->json([
            'status' => ucfirst($returnRequest->status),
            'return_id' => $returnRequest->return_id,
            'reason' => $returnRequest->reason,
            'rejection_reason' => $returnRequest->rejection_reason,
        ]);
    }
}
