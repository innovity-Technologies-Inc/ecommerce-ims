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

        if ($request->ajax()) {
            return view('admin.orders.partials.table', compact('orders'))->render();
        }

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load(['orderItems', 'user']);
        $statuses = $this->orderService->getStatusList();

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->orderService->updateOrderStatus(
            $order,
            $request->order_status,
            $request->has('email_notify')
        );

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    /**
     * Generate an invoice for the order.
     */
    public function generateInvoice(Order $order): RedirectResponse
    {
        $this->orderService->generateInvoice($order);

        return redirect()->back()->with('success', 'Invoice generated successfully!');
    }

    /**
     * Regenerate the invoice date.
     */
    public function regenerateInvoice(Order $order): RedirectResponse
    {
        $this->orderService->regenerateInvoice($order);

        return redirect()->back()->with('success', 'Invoice regenerated successfully!');
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
     * Remove the specified order.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $this->orderService->deleteOrder($order);

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully!');
    }
}
