<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Display a listing of orders.
     */
    public function index(): View
    {
        $orders = Order::latest()->paginate(20);

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
     * Remove the specified order.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $this->orderService->deleteOrder($order);

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully!');
    }
}
