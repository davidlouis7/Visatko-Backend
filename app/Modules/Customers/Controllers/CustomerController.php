<?php

namespace App\Modules\Customers\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Requests\StoreCustomerRequest;
use App\Modules\Customers\Requests\UpdateCustomerRequest;
use App\Modules\Customers\Resources\CustomerResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Customer::class);
        $search = request('search');
        $items = Customer::query()->when($search, fn ($q) => $q->where(fn ($inner) => $inner->where('full_name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))->withCount(['consultations', 'visaApplications'])->latest()->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($items, CustomerResource::collection($items->getCollection()));
    }

    public function show(Customer $customer): JsonResponse
    {
        Gate::authorize('view', $customer);

        return $this->success(CustomerResource::make($customer->loadCount(['consultations', 'visaApplications'])));
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::query()->create($request->validated());
        activity('admin')->causedBy($request->user())->performedOn($customer)->log('Customer created');

        return $this->success(CustomerResource::make($customer), 'Customer created successfully.', 201);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());
        activity('admin')->causedBy($request->user())->performedOn($customer)->log('Customer updated');

        return $this->success(CustomerResource::make($customer), 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): JsonResponse
    {
        Gate::authorize('delete', $customer);
        $customer->update(['is_active' => false]);
        $customer->delete();
        activity('admin')->causedBy(request()->user())->performedOn($customer)->log('Customer deactivated');

        return $this->success(null, 'Customer deactivated successfully.');
    }
}
