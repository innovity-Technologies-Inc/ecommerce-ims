<!-- ========== App Menu Start ========== -->
@php($gs = \App\HelperClass::generalSettings())
<div class="main-nav">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="{{ route('admin.dashboard') }}" class="logo-dark">
            <img src="{{ $gs->favicon ? asset('storage/'.$gs->favicon) : asset('admin_assets/assets/images/favicon.ico') }}" class="logo-sm" alt="logo sm" style="height: 30px;">
            <img src="{{ $gs->dark_logo ? asset('storage/'.$gs->dark_logo) : asset('admin_assets/assets/images/logo-dark.png') }}" class="logo-lg" alt="logo dark" style="height: 50px;">
        </a>

        <a href="{{ route('admin.dashboard') }}" class="logo-light">
            <img src="{{ $gs->favicon ? asset('storage/'.$gs->favicon) : asset('admin_assets/assets/images/favicon.ico') }}" class="logo-sm" alt="logo sm" style="height: 30px;">
            <img src="{{ $gs->dark_logo ? asset('storage/'.$gs->dark_logo) : asset('admin_assets/assets/images/logo-light.png') }}" class="logo-lg" alt="logo light" style="height: 50px;">
        </a>
    </div>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">General</li>

            @can('dashboard.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>
            @endcan

            @can('category.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.categories.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Category </span>
                </a>
            </li>
            @endcan

            @can('brand.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.brands.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:tag-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Brands </span>
                </a>
            </li>
            @endcan

            @can('products.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.products.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:t-shirt-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Products </span>
                </a>
            </li>
            @endcan

            @can('shipping_methods.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.shipping_methods.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:delivery-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Shipping Methods </span>
                </a>
            </li>
            @endcan

            @can('orders.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.orders.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:cart-large-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Orders </span>
                </a>
            </li>
            @endcan

            @can('returns.view')
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarReturns" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarReturns">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:restart-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Returns </span>
                </a>
                <div class="collapse" id="sidebarReturns">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.returns.requests') }}">Return Requests</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.returns.returned_products') }}">Returned Products</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.returns.wastages') }}">Wastages</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            @if(auth('admin')->user()->can('coupons.view') || auth('admin')->user()->can('flash_sale.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarPromotions" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPromotions">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:ticket-sale-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Promotions </span>
                </a>
                <div class="collapse" id="sidebarPromotions">
                    <ul class="nav sub-navbar-nav">
                        @can('coupons.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.coupons.index') }}">Coupons</a>
                        </li>
                        @endcan
                        @can('flash_sale.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.flash_sale.edit') }}">Flash Sale</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endif

            @if(auth('admin')->user()->can('sliders.view') || auth('admin')->user()->can('homepage_sections.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarHomepage" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarHomepage">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Homepage </span>
                </a>
                <div class="collapse" id="sidebarHomepage">
                    <ul class="nav sub-navbar-nav">
                        @can('sliders.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.sliders.index') }}">Sliders</a>
                        </li>
                        @endcan
                        @can('homepage_sections.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.sections.bestsellers') }}">Bestsellers</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.sections.edit', 'hot_deals') }}">Hot Deals</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.sections.edit', 'featured') }}">Featured</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.sections.edit', 'recently_added') }}">Recently Added</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.sections.edit', 'top_picks') }}">Top Picks</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endif

            <li class="menu-title">Inventory</li>

            @can('inventory.view')
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarInventoryReports" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarInventoryReports">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:graph-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Inventory</span>
                </a>
                <div class="collapse" id="sidebarInventoryReports">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.inventory.stock.index') }}">Stock</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.inventory.batches.index') }}">Batch Tracking</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.inventory.damaged.index') }}">Damaged Products</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            @can('po.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.inventory.po.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Purchase Orders </span>
                </a>
            </li>
            @endcan

            @can('warehouse.view')
            <li class="nav-item">
               <a class="nav-link" href="{{ route('admin.warehouses.index') }}">
                   <span class="nav-icon">
                       <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                   </span>
                   <span class="nav-text"> Warehouses </span>
               </a>
            </li>
            @endcan

            @can('supplier.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.suppliers.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Suppliers </span>
                </a>
            </li>
            @endcan

            <li class="menu-title mt-2">Management</li>

            @if(auth('admin')->user()->can('admins.view') || auth('admin')->user()->can('roles.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarUsers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsers">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:user-speak-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Admins </span>
                </a>
                <div class="collapse" id="sidebarUsers">
                    <ul class="nav sub-navbar-nav">
                        @can('admins.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.index') }}">Users</a>
                        </li>
                        @endcan
                        @can('roles.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.roles.index') }}">Roles</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endif

            @can('customers.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.customers.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Customers </span>
                </a>
            </li>
            @endcan

            @can('contact_messages.view')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.contact_messages.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:letter-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Contact Messages </span>
                </a>
            </li>
            @endcan

            <li class="menu-title mt-2">Settings</li>

            @can('settings.view')
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarSettings" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSettings">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Settings </span>
                </a>
                <div class="collapse" id="sidebarSettings">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.settings.general') }}">General Settings</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.settings.contact') }}">Contact Settings</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

        </ul>
    </div>
</div>
<!-- ========== App Menu End ========== -->
