<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CustomerManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(protected CustomerManagementService $customerService) {}

    /**
     * Display a listing of the customers.
     */
    public function index(): View
    {
        $customers = $this->customerService->getAllCustomers();

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
    public function toggleStatus(int $id): RedirectResponse
    {
        $this->customerService->toggleCustomerStatus($id);

        return back()->with([
            'message' => 'Customer status updated successfully',
            'alert-type' => 'success',
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
