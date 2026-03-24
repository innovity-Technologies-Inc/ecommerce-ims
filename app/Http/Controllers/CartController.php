<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\CartRequest;
use App\Http\Requests\Client\RemoveCartItemRequest;
use App\Http\Requests\Client\UpdateShippingRequest;
use App\Http\Requests\Client\UpdateCartRequest;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\ShippingMethodService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected CartService $cartService,
        protected ShippingMethodService $shippingMethodService
    ) {}

    /**
     * Display the cart page.
     */
    public function index(): View
    {
        $cartItems = $this->cartService->getCartItems();
        $shippingMethods = $this->shippingMethodService->getActiveMethods();
        $selectedShippingMethod = session('shipping_method_id')
            ? ShippingMethod::find(session('shipping_method_id'))
            : null;

        return view('client.cart', compact('cartItems', 'shippingMethods', 'selectedShippingMethod'));
    }

    /**
     * Update shipping method in session.
     */
    public function updateShippingMethod(UpdateShippingRequest $request): JsonResponse
    {
        $shippingMethod = ShippingMethod::find($request->shipping_method_id);
        session(['shipping_method_id' => $shippingMethod->id]);

        $cartTotal = $this->cartService->getCartTotal();
        $grandTotal = $cartTotal + $shippingMethod->price;

        return response()->json([
            'status' => 'success',
            'shipping_price' => number_format($shippingMethod->price, 2),
            'grand_total' => number_format($grandTotal, 2),
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function addToCart(CartRequest $request): JsonResponse
    {
        $result = $this->cartService->addToCart($request->validated());

        if ($result === true) {
            return response()->json([
                'status' => 'success',
                'message' => 'Product added to cart successfully!',
                'cart_count' => $this->cartService->getCartCount(),
                'mini_cart_html' => view('client.structure.mini-cart')->render(),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result,
        ], 422);
    }

    /**
     * Update the quantity of a cart item.
     */
    public function updateQuantity(UpdateCartRequest $request): JsonResponse
    {
        $result = $this->cartService->updateQuantity($request->cart_id, $request->quantity);

        if ($result === true) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cart updated successfully!',
                'cart_count' => $this->cartService->getCartCount(),
                'item_subtotal' => number_format($this->cartService->getItemSubtotal($request->cart_id), 2),
                'total' => number_format($this->cartService->getCartTotal(), 2),
                'mini_cart_html' => view('client.structure.mini-cart')->render(),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result,
        ], 422);
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(RemoveCartItemRequest $request): JsonResponse
    {
        $this->cartService->removeItem($request->cart_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart!',
            'cart_count' => $this->cartService->getCartCount(),
            'total' => number_format($this->cartService->getCartTotal(), 2),
            'mini_cart_html' => view('client.structure.mini-cart')->render(),
        ]);
    }
}
