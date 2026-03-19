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

    public function viewInvoice(string $orderId)
    {
        $order = $this->orderService->trackOrderById($orderId);

        if (! $order || $order->user_id !== Auth::guard('web')->id()) {
            abort(404);
        }

        // Auto-generate invoice if not exists when user tries to view it
        if (! $order->invoice_no) {
            $this->orderService->generateInvoice($order);
        }

        $order->load(['orderItems']);

        return view('client.orders.invoice-print', compact('order'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::guard('web')->id()],
            'mobile' => ['required', 'string', 'max:20'],
        ]);

        $user = Auth::guard('web')->user();
        $user->update($request->only(['name', 'email', 'mobile']));

        return redirect()->back()->with([
            'message' => 'Profile updated successfully',
            'alert-type' => 'success',
            'active_tab' => 'profile',
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
            ])->withInput()->with('active_tab', 'password');
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
            'active_tab' => 'password',
        ]);
    }

    public function addressUpdate(Request $request)
    {
        $request->validate([
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:20'],
        ]);

        $user = Auth::guard('web')->user();
        $user->update($request->only(['address', 'city', 'state', 'country', 'zip']));

        return redirect()->back()->with([
            'message' => 'Address updated successfully',
            'alert-type' => 'success',
            'active_tab' => 'address',
        ]);
    }
}
