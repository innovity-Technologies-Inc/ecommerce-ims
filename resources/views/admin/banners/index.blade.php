@extends('admin.structure.app')

@section('title', 'Manage Banners')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Manage Dynamic Banners</h4>
    </div>

    <div class="row">
        @php
            $descriptions = [
                'home_1_left' => 'Homepage Layout 1 - Left Small Banner',
                'home_1_middle' => 'Homepage Layout 1 - Middle Large Banner',
                'home_1_right' => 'Homepage Layout 1 - Right Small Banner',
                'home_2_full' => 'Homepage Layout 2 - Wide Full-width Banner',
                'cart_sidebar' => 'Shopping Cart - Sidebar Promotional Banner',
                'menu_banner' => 'Navigation Menu - Megamenu Sidebar Banner',
            ];

            $recommended_sizes = [
                'home_1_left' => '330x315 px',
                'home_1_middle' => '690x315 px',
                'home_1_right' => '330x315 px',
                'home_2_full' => '1410x230 px',
                'cart_sidebar' => '690x550 px',
                'menu_banner' => '1350x170 px',
            ];
        @endphp

        @foreach($banners as $banner)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-light-subtle d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">{{ strtoupper(str_replace('_', ' ', $banner->slug)) }}</h6>
                    <span class="badge bg-soft-info text-info">{{ $recommended_sizes[$banner->slug] ?? 'N/A' }}</span>
                </div>
                <div class="card-body text-center p-0 overflow-hidden" style="height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ str_contains($banner->image, 'client/') ? asset($banner->image) : \App\HelperClass::file_url($banner->image) }}" 
                         class="img-fluid" alt="Banner" style="max-height: 100%; object-fit: contain;">
                </div>
                <div class="card-body bg-white border-top">
                    <p class="text-muted small mb-3">{{ $descriptions[$banner->slug] ?? 'Dynamic banner placement.' }}</p>
                    <div class="d-grid">
                        <a href="{{ route('admin.banners.edit', $banner->slug) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-edit-alt me-1"></i> Update Banner
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
