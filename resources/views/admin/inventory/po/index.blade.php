@extends('admin.structure.master')

@section('title', 'Purchase Orders')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Purchase Orders</h4>
                    @can('inventory.po.create')
                    <div class="page-title-right">
                        <a href="{{ route('admin.inventory.po.create') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i> Create Purchase Order
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <input type="text" id="poSearch" class="form-control" placeholder="Search PO Number...">
                            </div>
                            <div class="col-md-2">
                                <select id="statusFilter" class="form-select">
                                    <option value="all">All Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Sent">Sent</option>
                                    <option value="Delivered">Delivered</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="supplierFilter" class="form-select select2">
                                    <option value="">All Suppliers</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="sortFilter" class="form-select">
                                    <option value="latest">Latest</option>
                                    <option value="oldest">Oldest</option>
                                </select>
                            </div>
                            <div class="col-md-3 text-end">
                                <button type="button" id="resetFilters" class="btn btn-soft-secondary">Reset</button>
                            </div>
                        </div>

                        <div id="poTableContainer">
                            @include('admin.inventory.po.partials.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        function fetchPOs() {
            let search = $('#poSearch').val();
            let status = $('#statusFilter').val();
            let supplier_id = $('#supplierFilter').val();
            let sort = $('#sortFilter').val();

            $('#poTableContainer').css('opacity', 0.5);

            $.ajax({
                url: "{{ route('admin.inventory.po.index') }}",
                data: { search, status, supplier_id, sort },
                success: function(response) {
                    $('#poTableContainer').html(response).css('opacity', 1);
                    window.history.pushState(null, null, `?search=${search}&status=${status}&supplier_id=${supplier_id}&sort=${sort}`);
                }
            });
        }

        $('#poSearch').on('keyup', debounce(fetchPOs, 500));
        $('#statusFilter, #supplierFilter, #sortFilter').on('change', fetchPOs);

        $('#resetFilters').click(function() {
            $('#poSearch').val('');
            $('#statusFilter').val('all');
            $('#supplierFilter').val('').trigger('change');
            $('#sortFilter').val('latest');
            fetchPOs();
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            $('#poTableContainer').css('opacity', 0.5);
            $.ajax({
                url: url,
                success: function(response) {
                    $('#poTableContainer').html(response).css('opacity', 1);
                }
            });
        });

        function debounce(func, wait) {
            let timeout;
            return function() {
                clearTimeout(timeout);
                timeout = setTimeout(func, wait);
            };
        }
    });
</script>
@endpush
