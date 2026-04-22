<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageCustomerRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::query()
            ->where('role', 'user')
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(ManageCustomerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $customer = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    public function show(User $customer): View
    {
        $customer = $this->ensureCustomer($customer);
        $customer->loadCount('orders');

        $orders = Order::query()
            ->where('user_id', $customer->id)
            ->latest()
            ->with('items.product')
            ->paginate(10);

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function edit(User $customer): View
    {
        $customer = $this->ensureCustomer($customer);

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(ManageCustomerRequest $request, User $customer): RedirectResponse
    {
        $customer = $this->ensureCustomer($customer);
        $validated = $request->validated();

        $customer->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! empty($validated['password'])) {
            $customer->password = Hash::make($validated['password']);
        }

        $customer->save();

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $customer): RedirectResponse
    {
        $customer = $this->ensureCustomer($customer);

        if ($customer->orders()->exists()) {
            return back()->with('error', 'Delete the customer orders first or keep the customer record for history.');
        }

        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    private function ensureCustomer(User $customer): User
    {
        abort_unless($customer->role === 'user', 404);

        return $customer;
    }
}
