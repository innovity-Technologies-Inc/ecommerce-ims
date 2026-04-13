@extends('admin.structure.app')

@section('content')
    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Message Details</h4>
                        <a href="{{ route('admin.contact_messages.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Customer Name</label>
                                <p class="fw-bold fs-16 mb-0">{{ $message->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Email Address</label>
                                <p class="fw-bold fs-16 mb-0">{{ $message->email }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Subject</label>
                                <p class="fw-bold fs-16 mb-0">{{ $message->subject }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Date Received</label>
                                <p class="fw-bold fs-16 mb-0">{{ $message->created_at->format('d M, Y h:i A') }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted">Message Content</label>
                            <div class="p-3 bg-light rounded border">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <form action="{{ route('admin.contact_messages.destroy', $message->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger confirmDelete">
                                    <i class="bx bx-trash me-1"></i> Delete Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
