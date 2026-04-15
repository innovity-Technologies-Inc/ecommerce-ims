@extends('admin.structure.app')

@section('title', 'Supplier Details')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Supplier: {{ $supplier->name }}</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                    @can('supplier.edit')
                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Edit Supplier">
                        <i class="bx bx-edit fs-16"></i>
                    </a>                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Vendor Information</h5>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Name:</label>
                        <div class="fw-bold fs-16">{{ $supplier->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Email:</label>
                        <div class="fw-bold">{{ $supplier->email }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Mobile:</label>
                        <div class="fw-bold">{{ $supplier->mobile }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Address:</label>
                        <div class="fw-bold">{{ $supplier->address }}</div>
                    </div>
                    <hr>
                    <div class="mb-0">
                        <label class="text-muted small mb-1">Avg Performance Score:</label>
                        @php
                            $score = $supplier->average_performance_score;
                            $badgeClass = 'bg-danger';
                            if ($score >= 80) $badgeClass = 'bg-success';
                            elseif ($score >= 50) $badgeClass = 'bg-warning text-dark';
                        @endphp
                        <div class="d-flex align-items-center">
                            <div class="badge {{ $badgeClass }} fs-16 px-3 py-2">
                                <iconify-icon icon="solar:star-bold" class="align-middle me-1"></iconify-icon>
                                {{ $score }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Purchase History</h5>
                    <div id="po-table-container">
                        @include('admin.inventory.suppliers.partials.po_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            let container = $('#po-table-container');

            container.css('opacity', '0.5');

            $.ajax({
                url: url,
                success: function(response) {
                    container.html(response);
                    container.css('opacity', '1');
                    // We don't pushState here to avoid changing the main page URL 
                    // unless we want full page sync for the PO list specifically.
                }
            });
        });
    });
</script>
@endsection
