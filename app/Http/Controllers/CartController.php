<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Requests\RemoveCartItemRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected CartService $cartService) {}

    /**
     * Display the cart page.
     */
    public function index(): View
    {
        $cartItems = $this->cartService->getCartItems();

        return view('client.cart', compact('cartItems'));
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
