@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">FAQs</h4>
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary btn-sm">Add New FAQ</a>
    </div>

    <div class="card">
        <div class="card-header border-bottom-0">
            <div class="row align-items-center g-2">
                <div class="col-lg-12">
                    <form id="faq-filter-form" class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by question or answer..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-soft-secondary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="faq-table-container">
            @include('admin.faqs.partials.table', ['faqs' => $faqs])
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const filterForm = $('#faq-filter-form');
        const tableContainer = $('#faq-table-container');

        filterForm.on('submit', function(e) {
            e.preventDefault();
            const url = "{{ route('admin.faqs.index') }}?" + $(this).serialize();
            
            tableContainer.css('opacity', '0.5');
            
            $.ajax({
                url: url,
                success: function(response) {
                    tableContainer.html(response);
                    tableContainer.css('opacity', '1');
                    window.history.pushState({}, '', url);
                }
            });
        });
    });
</script>
@endsection
