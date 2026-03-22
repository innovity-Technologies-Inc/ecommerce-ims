@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $title }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($role) ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}" method="POST">
                            @csrf
                            @if(isset($role))
                                @method('PUT')
                            @endif

                            <div class="mb-4">
                                <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name ?? '') }}" placeholder="Enter role name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Permissions</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAllGlobal">
                                        <label class="form-check-label fw-bold" for="checkAllGlobal">Check All Permissions</label>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach($groupedPermissions as $menu => $permissions)
                                        <div class="col-md-4 mb-4">
                                            <div class="card border shadow-none h-100">
                                                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 text-primary">{{ ucwords(str_replace('_', ' ', $menu)) }}</h6>
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input checkAllMenu" type="checkbox" data-menu="{{ $menu }}" id="checkAll_{{ $menu }}">
                                                        <label class="form-check-label small fw-bold" for="checkAll_{{ $menu }}">All</label>
                                                    </div>
                                                </div>
                                                <div class="card-body py-2">
                                                    @foreach($permissions as $permission)
                                                        @php
                                                            $op = explode('.', $permission->name)[1];
                                                            $isChecked = isset($role) && $role->hasPermissionTo($permission->name);
                                                        @endphp
                                                        <div class="form-check mb-1">
                                                            <input class="form-check-input permission-checkbox {{ $menu }}-checkbox" 
                                                                   type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $permission->name }}" 
                                                                   id="perm_{{ $permission->id }}"
                                                                   {{ $isChecked ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                                                {{ ucfirst($op) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-3">
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">{{ isset($role) ? 'Update Role' : 'Create Role' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Global Check All
        $('#checkAllGlobal').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.permission-checkbox').prop('checked', isChecked);
            $('.checkAllMenu').prop('checked', isChecked);
        });

        // Menu Check All
        $('.checkAllMenu').on('change', function() {
            const menu = $(this).data('menu');
            const isChecked = $(this).is(':checked');
            $(`.${menu}-checkbox`).prop('checked', isChecked);
            updateGlobalCheckAll();
        });

        // Individual Checkbox Change
        $('.permission-checkbox').on('change', function() {
            const menu = $(this).attr('class').split(' ').find(c => c.endsWith('-checkbox')).replace('-checkbox', '');
            const allChecked = $(`.${menu}-checkbox:checked`).length === $(`.${menu}-checkbox`).length;
            $(`#checkAll_${menu}`).prop('checked', allChecked);
            updateGlobalCheckAll();
        });

        function updateGlobalCheckAll() {
            const allCount = $('.permission-checkbox').length;
            const checkedCount = $('.permission-checkbox:checked').length;
            $('#checkAllGlobal').prop('checked', allCount === checkedCount);
        }

        // Initialize Menu Check All states
        $('.checkAllMenu').each(function() {
            const menu = $(this).data('menu');
            const allChecked = $(`.${menu}-checkbox:checked`).length === $(`.${menu}-checkbox`).length && $(`.${menu}-checkbox`).length > 0;
            $(this).prop('checked', allChecked);
        });
        updateGlobalCheckAll();
    });
</script>
@endsection
