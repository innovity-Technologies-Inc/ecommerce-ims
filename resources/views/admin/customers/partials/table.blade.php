<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
        <tr>
            <th>SL</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sl = \App\HelperClass::indexNumberSerialization($customers);
        @endphp
        @forelse($customers as $customer)
            <tr>
                <td>{{ $sl++ }}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="ms-2">
                            <h6 class="mb-0">{{ $customer->name }}</h6>
                            <small class="text-muted">Joined: {{ $customer->created_at->format('d M, Y') }}</small>
                        </div>
                    </div>
                </td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->mobile ?? 'N/A' }}</td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input status-toggle" type="checkbox" role="switch" 
                            id="statusSwitch{{ $customer->id }}" 
                            data-id="{{ $customer->id }}"
                            {{ $customer->status ? 'checked' : '' }} {{ auth('admin')->user()->can('customers.edit') ? '' : 'disabled' }}>
                    </div>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-soft-primary btn-sm" title="View Profile">
                            <i class="bx bx-show fs-16"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No customers found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted">
            Showing <span class="fw-semibold">{{ $customers->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $customers->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $customers->total() }}</span> Results
        </div>
        <div>
            {{ $customers->appends(request()->all())->links() }}
        </div>
    </div>
</div>
