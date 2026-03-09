<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService  $cartService,
        protected OrderService $orderService
    )
    {
    }

    /**
     * Display the checkout page.
     */
    public function index(): View|RedirectResponse
    {
        $cartItems = $this->cartService->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        if (!session('shipping_method_id')) {
            return redirect()->route('cart.index')->with('error', 'Please select a shipping method before checkout.');
        }

        $selectedShippingMethod = ShippingMethod::find(session('shipping_method_id'));
        if (!$selectedShippingMethod) {
            return redirect()->route('cart.index')->with('error', 'Selected shipping method is no longer available.');
        }

        $user = Auth::guard('web')->user();
        $subtotal = $this->cartService->getCartTotal();
        $grandTotal = $subtotal + $selectedShippingMethod->price;

        return view('client.checkout.index', compact('user', 'cartItems', 'subtotal', 'selectedShippingMethod', 'grandTotal'));
    }

    /**
     * Process the order.
     */
    public function store(CheckoutRequest $request): RedirectResponse
    {
        try {
            $order = $this->orderService->placeOrder($request->validated());

            // Redirect to success page or order details (to be implemented)
            return redirect()->route('checkout.success', ['order_id' => $order->order_id])
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display success page.
     */
    public function success(string $order_id): View
    {
        return view('client.checkout.success', compact('order_id'));
    }
}
