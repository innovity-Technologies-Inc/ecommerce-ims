<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index(): View
    {
        $cartItems = $this->cartService->getCartItems();

        return view('client.cart', compact('cartItems'));
    }

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

    public function updateQuantity(Request $request): JsonResponse
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $result = $this->cartService->updateQuantity($request->cart_id, $request->quantity);

        if ($result === true) {
            $cartItems = $this->cartService->getCartItems();
            $total = $cartItems->sum('subtotal');

            return response()->json([
                'status' => 'success',
                'message' => 'Cart updated successfully!',
                'cart_count' => $this->cartService->getCartCount(),
                'item_subtotal' => number_format($cartItems->firstWhere('id', $request->cart_id)->subtotal, 2),
                'total' => number_format($total, 2),
                'mini_cart_html' => view('client.structure.mini-cart')->render(),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result,
        ], 422);
    }

    public function removeItem(Request $request): JsonResponse
    {
        $request->validate(['cart_id' => 'required|exists:carts,id']);

        $this->cartService->removeItem($request->cart_id);

        $cartItems = $this->cartService->getCartItems();
        $total = $cartItems->sum('subtotal');

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart!',
            'cart_count' => $this->cartService->getCartCount(),
            'total' => number_format($total, 2),
            'mini_cart_html' => view('client.structure.mini-cart')->render(),
        ]);
    }
}
