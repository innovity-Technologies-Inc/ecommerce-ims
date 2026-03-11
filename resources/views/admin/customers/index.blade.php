@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Customer List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered mb-0">
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
                                @forelse($customers as $index => $customer)
                                    <tr>
                                        <td>{{ \App\HelperClass::indexNumberSerialization($customers) + $index }}</td>
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
                                            <form action="{{ route('admin.customers.toggle-status', $customer->id) }}" method="POST">
                                                @csrf
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch{{ $customer->id }}" {{ $customer->status ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <label class="form-check-label" for="statusSwitch{{ $customer->id }}">
                                                        <span class="badge {{ $customer->status ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $customer->status ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-soft-primary btn-sm" title="View Profile">
                                                    <i class="bx bx-show fs-16"></i>
                                                </a>
                                                <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" title="Delete Customer">
                                                        <i class="bx bx-trash fs-16"></i>
                                                    </button>
                                                </form>
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
                        <div class="mt-3">
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
