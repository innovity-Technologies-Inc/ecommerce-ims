<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function accountInformation()
    {
        $title = 'Account Information';
        $section = 'Account';

        return view('client.auth.account_info', compact('title', 'section'));
    }

    public function orderHistory()
    {
        $title = 'Order History';
        $section = 'Orders';
        $orders = $this->orderService->getUserOrders(Auth::guard('web')->id());

        return view('client.account.orders', compact('title', 'section', 'orders'));
    }

    public function orderDetails(string $orderId)
    {
        $title = 'Order Details - '.$orderId;
        $section = 'Orders';
        $order = $this->orderService->trackOrderById($orderId);

        if (! $order || $order->user_id !== Auth::guard('web')->id()) {
            return redirect()->route('user.orders')->with('error', 'Order not found.');
        }

        return view('client.account.order-details', compact('title', 'section', 'order'));
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::guard('web')->user();
        $user->update($request->all());

        return redirect()->back()->with([
            'message' => 'Profile updated successfully',
            'alert-type' => 'success',
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::guard('web')->user();

        // Check old password
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Regenerate session
        $request->session()->regenerate();

        return back()->with([
            'message' => 'Password changed successfully',
            'alert-type' => 'success',
        ]);
    }

    public function addressUpdate(Request $request)
    {
        $user = Auth::guard('web')->user();
        $user->update($request->all());

        return redirect()->back()->with([
            'message' => 'Address updated successfully',
            'alert-type' => 'success',
        ]);
    }
}
