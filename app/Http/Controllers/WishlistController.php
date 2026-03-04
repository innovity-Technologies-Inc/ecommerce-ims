<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with(['product.primaryImage', 'product.variants'])
            ->latest()
            ->get();

        return view('client.wishlist', [
            'wishlistItems' => $wishlistItems,
            'title' => 'Wishlist',
            'section' => 'Wishlist'
        ]);
    }

    /**
     * Add a product to the wishlist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return back()->with([
                'message' => 'Product already in your wishlist',
                'alert-type' => 'info',
            ]);
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
        ]);

        return back()->with([
            'message' => 'Product added to wishlist successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove a product from the wishlist.
     */
    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $wishlist->delete();

        return back()->with([
            'message' => 'Product removed from wishlist',
            'alert-type' => 'success',
        ]);
    }
}
