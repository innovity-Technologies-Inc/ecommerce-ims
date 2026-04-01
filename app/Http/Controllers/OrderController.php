<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $orders = $this->orderService->getAllOrders($request->all());
        $statuses = $this->orderService->getStatusList();
        $paymentMethods = $this->orderService->getPaymentMethods();
        $paymentStatuses = $this->orderService->getPaymentStatuses();

        if ($request->ajax()) {
            return view('admin.orders.partials.table', compact('orders'))->render();
        }

        return view('admin.orders.index', compact('orders', 'statuses', 'paymentMethods', 'paymentStatuses'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load(['orderItems', 'user']);
        $availableStatuses = $this->orderService->getAvailableTransitions($order->order_status);

        return view('admin.orders.show', compact('order', 'availableStatuses'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orderService->updateOrderStatus(
                $order,
                $request->order_status,
                $request->has('email_notify'),
                $request->rejection_reason,
                $request->items ?? []
            );

            return redirect()->back()->with([
                'message' => 'Order status updated successfully!',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            Log::error('Order Update Status Error: '.$e->getMessage());

            return redirect()->back()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Generate an invoice for the order.
     */
    public function generateInvoice(Order $order): RedirectResponse
    {
        $this->orderService->generateInvoice($order);

        return redirect()->back()->with([
            'message' => 'Invoice generated successfully!',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Regenerate the invoice date.
     */
    public function regenerateInvoice(Order $order): RedirectResponse
    {
        $this->orderService->regenerateInvoice($order);

        return redirect()->back()->with([
            'message' => 'Invoice regenerated successfully!',
            'alert-type' => 'success',
        ]);
    }

    /**
     * View/Print the invoice.
     */
    public function viewInvoice(Order $order): View
    {
        if (! $order->invoice_no) {
            $this->orderService->generateInvoice($order);
        }

        $order->load(['orderItems']);

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Get warehouses for an order item.
     */
    public function getWarehouses(Request $request): \Illuminate\Http\JsonResponse
    {
        $warehouses = $this->orderService->getWarehousesForItem($request->product_id, $request->variant_id);

        return response()->json($warehouses);
    }

    /**
     * Get batches for an order item in a warehouse.
     */
    public function getBatches(Request $request): \Illuminate\Http\JsonResponse
    {
        $batches = $this->orderService->getBatchesForItemInWarehouse($request->warehouse_id, $request->product_id, $request->variant_id);

        return response()->json($batches);
    }

    /**
     * Get available serials for a batch.
     */
    public function getSerials(Request $request): \Illuminate\Http\JsonResponse
    {
        $serials = $this->orderService->getAvailableSerials($request->batch_id, $request->product_id, $request->variant_id);

        return response()->json($serials);
    }
}
