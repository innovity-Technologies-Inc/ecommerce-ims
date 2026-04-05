<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCartItems(): Collection
    {
        $userId = Auth::guard('web')->id();
        $sessionId = Session::getId();

        $query = Cart::with(['product', 'variant', 'product.primaryImage']);

        if ($userId) {
            $items = $query->where('user_id', $userId)->get();
        } else {
            $items = $query->where('session_id', $sessionId)->get();
        }

        return $items->map(function ($item) {
            if ($item->variant) {
                $regularPrice = $item->variant->regular_price ?? $item->product->regular_price;
                // Priority: Flash Discount > Standard Discount > Regular Price
                if ($item->product->is_flash_sale && $item->variant->flash_discount_price > 0) {
                    $price = $item->variant->flash_discount_price;
                } elseif ($item->variant->discount_price > 0) {
                    $price = $item->variant->discount_price;
                } else {
                    $price = $regularPrice;
                }
            } else {
                $regularPrice = $item->product->regular_price;
                // Priority: Flash Discount > Standard Discount > Regular Price
                if ($item->product->is_flash_sale && $item->product->flash_discount_price > 0) {
                    $price = $item->product->flash_discount_price;
                } elseif ($item->product->discount_price > 0) {
                    $price = $item->product->discount_price;
                } else {
                    $price = $regularPrice;
                }
            }

            $productDiscount = (float) $regularPrice - (float) $price;

            $variantName = $item->variant ? $item->variant->variant_name : null;
            $variantDetails = null;
            if ($item->variant) {
                $details = [];
                if ($item->variant->size) {
                    $details[] = $item->variant->size;
                }
                if ($item->variant->color) {
                    $details[] = $item->variant->color;
                }

                $variantDetails = count($details) > 0 ? implode(' / ', $details) : $item->variant->variant_name;
            }

            return (object) [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_slug' => $item->product->slug,
                'variant_id' => $item->product_variant_id,
                'variant_name' => $variantName,
                'variant_details' => $variantDetails,
                'image' => $item->product->primaryImage ? $item->product->primaryImage->image_path : null,
                'regular_price' => (float) $regularPrice,
                'product_discount' => (float) $productDiscount,
                'price' => (float) $price,
                'quantity' => $item->quantity,
                'subtotal' => (float) $price * $item->quantity,
                'stock' => $item->variant ? $item->variant->stock : 0,
            ];
        });
    }

    public function addToCart(array $data): bool|string
    {
        $userId = Auth::guard('web')->id();
        $sessionId = Session::getId();

        $product = Product::findOrFail($data['product_id']);
        $variant = null;

        if (isset($data['product_variant_id'])) {
            $variant = ProductVariant::findOrFail($data['product_variant_id']);
            if ($variant->stock < $data['quantity']) {
                return 'Insufficient stock for selected variant.';
            }
        } else {
            // Check if product has variants but none selected
            if ($product->variants()->count() > 0) {
                return 'Please select a variant.';
            }
            // Add basic product stock check here if needed
        }

        $cartItem = Cart::where('product_id', $data['product_id'])
            ->where('product_variant_id', $data['product_variant_id'] ?? null)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $data['quantity'];
            if ($variant && $variant->stock < $newQuantity) {
                return 'Cannot add more. Insufficient stock.';
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'product_id' => $data['product_id'],
                'product_variant_id' => $data['product_variant_id'] ?? null,
                'quantity' => $data['quantity'],
            ]);
        }

        return true;
    }

    public function updateQuantity(int $cartId, int $quantity): bool|string
    {
        $userId = Auth::guard('web')->id();
        $sessionId = Session::getId();

        $cartItem = Cart::where('id', $cartId)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->firstOrFail();

        if ($cartItem->product_variant_id) {
            $variant = ProductVariant::find($cartItem->product_variant_id);
            if ($variant && $variant->stock < $quantity) {
                return 'Insufficient stock.';
            }
        }

        $cartItem->update(['quantity' => $quantity]);

        return true;
    }

    public function removeItem(int $cartId): void
    {
        $userId = Auth::guard('web')->id();
        $sessionId = Session::getId();

        Cart::where('id', $cartId)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->delete();
    }

    public function clearCart(): void
    {
        $userId = Auth::guard('web')->id();
        $sessionId = Session::getId();

        if ($userId) {
            Cart::where('user_id', $userId)->delete();
        } else {
            Cart::where('session_id', $sessionId)->delete();
        }
    }

    public function syncCartOnLogin(?string $oldSessionId = null): void
    {
        $userId = Auth::guard('web')->id();
        $sessionId = $oldSessionId ?? Session::getId();

        if (! $userId) {
            return;
        }

        $sessionItems = Cart::where('session_id', $sessionId)->get();

        foreach ($sessionItems as $item) {
            $existingItem = Cart::where('user_id', $userId)
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $item->quantity,
                ]);
                $item->delete();
            } else {
                $item->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            }
        }
    }

    public function getCartCount(): int
    {
        $userId = Auth::guard('web')->id();
        $sessionId = Session::getId();

        if ($userId) {
            return Cart::where('user_id', $userId)->sum('quantity');
        }

        return Cart::where('session_id', $sessionId)->sum('quantity');
    }

    /**
     * Get the total price of all items in the cart.
     */
    public function getCartTotal(): float
    {
        return (float) $this->getCartItems()->sum('subtotal');
    }

    /**
     * Get subtotal for a specific cart item.
     */
    public function getItemSubtotal(int $cartId): float
    {
        $item = $this->getCartItems()->firstWhere('id', $cartId);

        return $item ? (float) $item->subtotal : 0.0;
    }
}
