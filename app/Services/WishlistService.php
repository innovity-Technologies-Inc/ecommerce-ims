<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class WishlistService
{
    /**
     * Get the user's wishlist items.
     */
    public function getWishlistItems(): Collection
    {
        return Wishlist::where('user_id', Auth::id())
            ->with(['product.primaryImage', 'product.variants'])
            ->latest()
            ->get();
    }

    /**
     * Add a product to the wishlist.
     */
    public function addToWishlist(array $data): Wishlist
    {
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $data['product_id'])
            ->first();

        if ($exists) {
            return $exists;
        }

        return Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $data['product_id'],
        ]);
    }

    /**
     * Remove an item from the wishlist.
     */
    public function removeFromWishlist(int $id): void
    {
        Wishlist::where('user_id', Auth::id())->where('id', $id)->delete();
    }
}
