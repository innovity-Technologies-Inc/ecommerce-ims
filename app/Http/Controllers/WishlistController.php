<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Services\WishlistService;

class WishlistController extends Controller
{
    public function __construct(protected WishlistService $wishlistService) {}

    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        $wishlistItems = $this->wishlistService->getWishlistItems();

        return view('client.wishlist', [
            'wishlistItems' => $wishlistItems,
            'title' => 'Wishlist',
            'section' => 'Wishlist',
        ]);
    }

    /**
     * Add a product to the wishlist.
     */
    public function store(WishlistRequest $request)
    {
        $this->wishlistService->addToWishlist($request->validated());

        return back()->with([
            'message' => 'Wishlist updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove a product from the wishlist.
     */
    public function destroy(int $id)
    {
        $this->wishlistService->removeFromWishlist($id);

        return back()->with([
            'message' => 'Product removed from wishlist',
            'alert-type' => 'success',
        ]);
    }
}
