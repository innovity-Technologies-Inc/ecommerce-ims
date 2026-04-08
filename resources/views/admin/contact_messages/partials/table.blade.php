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
                            <button type="button" class="btn btn-soft-success btn-sm mark-as-read" 
                                data-id="{{ $message->id }}" 
                                data-url="{{ route('admin.contact_messages.read', $message->id) }}"
                                title="Mark as Read">
                                <i class="bx bx-check-double fs-16"></i>
                            </button>
                        @endif
                        @can('contact_messages.delete')
                        <form action="{{ route('admin.contact_messages.destroy', $message->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" title="Delete Message">
                                <i class="bx bx-trash fs-16"></i>
                            </button>
                        </form>
                        @endcan
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
    {{ $messages->appends(request()->all())->links() }}
</div>
