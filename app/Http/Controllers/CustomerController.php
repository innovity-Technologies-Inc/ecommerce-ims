<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\ProfileUpdateRequest;
use App\Http\Requests\Client\UpdateAddressRequest;
use App\Http\Requests\Client\UpdatePasswordRequest;
use App\Services\CustomerProfileService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected CustomerProfileService $profileService
    ) {}

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
            return redirect()->route('user.orders')->with([
                'message' => 'Order not found.',
                'alert-type' => 'error',
            ]);
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

    public function profileUpdate(ProfileUpdateRequest $request): RedirectResponse
    {
        $userId = Auth::guard('web')->id();
        $this->profileService->updateProfile($userId, $request->validated());

        return redirect()->back()->with([
            'message' => 'Profile updated successfully',
            'alert-type' => 'success',
            'active_tab' => 'profile',
        ]);
    }

    public function changePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $userId = Auth::guard('web')->id();
        $result = $this->profileService->updatePassword(
            $userId,
            $request->password,
            $request->current_password
        );

        if (! $result['status']) {
            $errors = [];
            if (isset($result['error_type'])) {
                $errors[$result['error_type']] = $result['message'];
            } else {
                return back()->with([
                    'message' => $result['message'],
                    'alert-type' => 'error',
                    'active_tab' => 'password',
                ]);
            }

            return back()->withErrors($errors)->withInput()->with('active_tab', 'password');
        }

        // Regenerate session if password changed
        session()->regenerate();

        return back()->with([
            'message' => 'Password changed successfully',
            'alert-type' => 'success',
            'active_tab' => 'password',
        ]);
    }

    public function addressUpdate(UpdateAddressRequest $request): RedirectResponse
    {
        $userId = Auth::guard('web')->id();
        $this->profileService->updateAddress($userId, $request->validated());

        return redirect()->back()->with([
            'message' => 'Address updated successfully',
            'alert-type' => 'success',
            'active_tab' => 'address',
        ]);
    }
}
