<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CustomerManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(protected CustomerManagementService $customerService) {}

    /**
     * Display a listing of registered customers.
     */
    public function index(Request $request)
    {
        $customers = $this->customerService->getAllCustomers($request->all());

        if ($request->ajax()) {
            return view('admin.customers.partials.table', compact('customers'))->render();
        }

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display the specified customer profile and order history.
     */
    public function show(int $id): View
    {
        $customer = $this->customerService->getCustomerWithOrders($id);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Toggle customer status.
     */
    public function toggleStatus(int $id): \Illuminate\Http\JsonResponse
    {
        $this->customerService->toggleCustomerStatus($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Customer status updated successfully',
        ]);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->customerService->deleteCustomer($id);

        return redirect()->route('admin.customers.index')->with([
            'message' => 'Customer deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
