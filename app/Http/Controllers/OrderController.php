<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $statuses = $this->orderService->getStatusList();
        unset($statuses['Pending']);

        return view('admin.orders.show', compact('order', 'statuses'));
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
                $request->has('email_notify')
            );

            return redirect()->back()->with([
                'message' => 'Order status updated successfully!',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
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
}
