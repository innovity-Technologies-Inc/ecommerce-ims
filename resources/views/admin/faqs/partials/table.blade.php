<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th>#</th>
                <th>Question</th>
                <th>Status</th>
                <th>Order</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($faqs as $faq)
                <tr>
                    <td>{{ \App\HelperClass::indexNumberSerialization($faqs) + $loop->index }}</td>
                    <td>
                        <span class="text-dark fw-medium">{{ Str::limit($faq->question, 50) }}</span>
                    </td>
                    <td>
                        @if($faq->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $faq->sort_order }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="Edit FAQ">
                                <iconify-icon icon="solar:pen-new-square-broken" class="fs-16"></iconify-icon>
                            </a>
                            <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" data-bs-toggle="tooltip" title="Delete FAQ">
                                    <iconify-icon icon="solar:trash-bin-trash-broken" class="fs-16"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">No FAQs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    {{ $faqs->appends(request()->all())->links() }}
</div>
