<div class="table-responsive">
    <table class="table table-centered mb-0">
        <thead class="bg-light-subtle">
        <tr>
            <th>SL</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sl = \App\HelperClass::indexNumberSerialization($messages);
        @endphp
        @forelse($messages as $message)
            <tr class="{{ !$message->is_read ? 'fw-bold' : '' }}">
                <td>{{ $sl++ }}</td>
                <td>{{ $message->created_at->format('d M, Y h:i A') }}</td>
                <td>
                    <div>
                        <h6 class="mb-0">{{ $message->name }}</h6>
                        <small class="text-muted">{{ $message->email }}</small>
                    </div>
                </td>
                <td>{{ $message->subject }}</td>
                <td>
                    @if(!$message->is_read)
                        <span class="badge bg-warning">Unread</span>
                    @else
                        <span class="badge bg-success">Read</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.contact_messages.show', $message->id) }}" class="btn btn-soft-primary btn-sm" title="View Message">
                            <i class="bx bx-show fs-16"></i>
                        </a>
                        @if(!$message->is_read)
                            <form action="{{ route('admin.contact_messages.read', $message->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-soft-success btn-sm" title="Mark as Read">
                                    <i class="bx bx-check-double fs-16"></i>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.contact_messages.destroy', $message->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" title="Delete Message">
                                <i class="bx bx-trash fs-16"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No messages found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted">
            Showing <span class="fw-semibold">{{ $messages->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $messages->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $messages->total() }}</span> Results
        </div>
        <div>
            {{ $messages->appends(request()->all())->links() }}
        </div>
    </div>
</div>
