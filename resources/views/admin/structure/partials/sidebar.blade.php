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
                <a class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>
            @endcan

            @can('category.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Category </span>
                </a>
            </li>
            @endcan

            @can('brand.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:tag-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Brands </span>
                </a>
            </li>
            @endcan

            @can('products.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.products.*') && !Request::routeIs('admin.products.best-selling') && !Request::routeIs('admin.products.low-stock') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:t-shirt-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Products </span>
                </a>
            </li>
            @endcan

            @can('shipping_methods.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.shipping_methods.*') ? 'active' : '' }}" href="{{ route('admin.shipping_methods.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:delivery-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Shipping Methods </span>
                </a>
            </li>
            @endcan

            @can('orders.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:cart-large-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Orders </span>
                </a>
            </li>
            @endcan

            @can('returns.view')
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ Request::routeIs('admin.returns.*') && !Request::routeIs('admin.returns.wastages') ? '' : 'collapsed' }}" href="#sidebarReturns" data-bs-toggle="collapse" role="button" aria-expanded="{{ Request::routeIs('admin.returns.*') && !Request::routeIs('admin.returns.wastages') ? 'true' : 'false' }}" aria-controls="sidebarReturns">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:restart-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Returns </span>
                </a>
                <div class="collapse {{ Request::routeIs('admin.returns.*') && !Request::routeIs('admin.returns.wastages') ? 'show' : '' }}" id="sidebarReturns">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.returns.requests') || Request::routeIs('admin.returns.show_request') ? 'active' : '' }}" href="{{ route('admin.returns.requests') }}">Return Requests</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.returns.returned_products') ? 'active' : '' }}" href="{{ route('admin.returns.returned_products') }}">Returned Products</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

            @if(auth('admin')->user()->can('coupons.view') || auth('admin')->user()->can('flash_sale.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ Request::routeIs('admin.coupons.*') || Request::routeIs('admin.flash_sale.*') ? '' : 'collapsed' }}" href="#sidebarPromotions" data-bs-toggle="collapse" role="button" aria-expanded="{{ Request::routeIs('admin.coupons.*') || Request::routeIs('admin.flash_sale.*') ? 'true' : 'false' }}" aria-controls="sidebarPromotions">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:ticket-sale-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Promotions </span>
                </a>
                <div class="collapse {{ Request::routeIs('admin.coupons.*') || Request::routeIs('admin.flash_sale.*') ? 'show' : '' }}" id="sidebarPromotions">
                    <ul class="nav sub-navbar-nav">
                        @can('coupons.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">Coupons</a>
                        </li>
                        @endcan
                        @can('flash_sale.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.flash_sale.*') ? 'active' : '' }}" href="{{ route('admin.flash_sale.edit') }}">Flash Sale</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endif

            @if(auth('admin')->user()->can('sliders.view') || auth('admin')->user()->can('homepage_sections.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ Request::routeIs('admin.sliders.*') || Request::routeIs('admin.sections.*') ? '' : 'collapsed' }}" href="#sidebarHomepage" data-bs-toggle="collapse" role="button" aria-expanded="{{ Request::routeIs('admin.sliders.*') || Request::routeIs('admin.sections.*') ? 'true' : 'false' }}" aria-controls="sidebarHomepage">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Homepage </span>
                </a>
                <div class="collapse {{ Request::routeIs('admin.sliders.*') || Request::routeIs('admin.sections.*') ? 'show' : '' }}" id="sidebarHomepage">
                    <ul class="nav sub-navbar-nav">
                        @can('sliders.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.sliders.*') ? 'active' : '' }}" href="{{ route('admin.sliders.index') }}">Sliders</a>
                        </li>
                        @endcan
                        @can('homepage_sections.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.sections.bestsellers') ? 'active' : '' }}" href="{{ route('admin.sections.bestsellers') }}">Bestsellers</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::is('admin/sections/hot_deals*') ? 'active' : '' }}" href="{{ route('admin.sections.edit', 'hot_deals') }}">Hot Deals</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::is('admin/sections/featured*') ? 'active' : '' }}" href="{{ route('admin.sections.edit', 'featured') }}">Featured</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::is('admin/sections/recently_added*') ? 'active' : '' }}" href="{{ route('admin.sections.edit', 'recently_added') }}">Recently Added</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::is('admin/sections/top_picks*') ? 'active' : '' }}" href="{{ route('admin.sections.edit', 'top_picks') }}">Top Picks</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endif

            <li class="menu-title mt-2">Inventory</li>

            @can('warehouse.view')
            <li class="nav-item">
               <a class="nav-link {{ Request::routeIs('admin.warehouses.*') ? 'active' : '' }}" href="{{ route('admin.warehouses.index') }}">
                   <span class="nav-icon">
                       <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                   </span>
                   <span class="nav-text"> Warehouses </span>
               </a>
            </li>
            @endcan

            @can('supplier.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.suppliers.*') ? 'active' : '' }}" href="{{ route('admin.suppliers.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Suppliers </span>
                </a>
            </li>
            @endcan

            @can('stock_adjustment.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.inventory.adjustment.*') ? 'active' : '' }}" href="{{ route('admin.inventory.adjustment.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:settings-minimalistic-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Stock Adjustment </span>
                </a>
            </li>
            @endcan

            @can('po.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.inventory.po.*') ? 'active' : '' }}" href="{{ route('admin.inventory.po.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Purchase Orders </span>
                </a>
            </li>
            @endcan

            @can('stock_report.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.inventory.stock.*') ? 'active' : '' }}" href="{{ route('admin.inventory.stock.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Stock Report </span>
                </a>
            </li>
            @endcan

            @can('batch_tracking.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.inventory.batches.*') ? 'active' : '' }}" href="{{ route('admin.inventory.batches.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:routing-2-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Batch Tracking </span>
                </a>
            </li>
            @endcan

            @can('damaged_products.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.inventory.damaged.*') ? 'active' : '' }}" href="{{ route('admin.inventory.damaged.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:danger-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Damaged Products </span>
                </a>
            </li>
            @endcan

            @can('supplier_rma.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.inventory.rma.*') ? 'active' : '' }}" href="{{ route('admin.inventory.rma.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:undo-left-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Supplier RMA </span>
                </a>
            </li>
            @endcan

            @can('wastage.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.returns.wastages') || Request::routeIs('admin.wastage.*') ? 'active' : '' }}" href="{{ route('admin.returns.wastages') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:trash-bin-trash-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Wastages </span>
                </a>
            </li>
            @endcan

            @can('reports.view')
            <li class="menu-title mt-2">Reports</li>

            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.reports.sales.*') ? 'active' : '' }}" href="{{ route('admin.reports.sales') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:graph-up-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Sales Reports </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.reports.inventory.*') ? 'active' : '' }}" href="{{ route('admin.reports.inventory') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:box-minimalistic-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Inventory Valuation </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.reports.stock.*') ? 'active' : '' }}" href="{{ route('admin.reports.stock') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Stock Reports </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.reports.warehouse-performance.*') ? 'active' : '' }}" href="{{ route('admin.reports.warehouse-performance') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:chart-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Warehouse Performance </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.reports.customers.*') ? 'active' : '' }}" href="{{ route('admin.reports.customers.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Customer Reports </span>
                </a>
            </li>
            @endcan

            <li class="menu-title mt-2">Management</li>

            @if(auth('admin')->user()->can('admins.view') || auth('admin')->user()->can('roles.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ Request::routeIs('admin.index') || Request::routeIs('admin.create') || Request::routeIs('admin.edit') || Request::routeIs('admin.roles.*') ? '' : 'collapsed' }}" href="#sidebarUsers" data-bs-toggle="collapse" role="button" aria-expanded="{{ Request::routeIs('admin.index') || Request::routeIs('admin.create') || Request::routeIs('admin.edit') || Request::routeIs('admin.roles.*') ? 'true' : 'false' }}" aria-controls="sidebarUsers">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:user-speak-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Admins </span>
                </a>
                <div class="collapse {{ Request::routeIs('admin.index') || Request::routeIs('admin.create') || Request::routeIs('admin.edit') || Request::routeIs('admin.roles.*') ? 'show' : '' }}" id="sidebarUsers">
                    <ul class="nav sub-navbar-nav">
                        @can('admins.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.index') || Request::routeIs('admin.create') || Request::routeIs('admin.edit') ? 'active' : '' }}" href="{{ route('admin.index') }}">Users</a>
                        </li>
                        @endcan
                        @can('roles.view')
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">Roles</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endif

            @can('customers.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Customers </span>
                </a>
            </li>
            @endcan

            @can('contact_messages.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.contact_messages.*') ? 'active' : '' }}" href="{{ route('admin.contact_messages.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:letter-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Contact Messages </span>
                </a>
            </li>
            @endcan

            @can('settings.view')
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.faqs.*') ? 'active' : '' }}" href="{{ route('admin.faqs.index') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:question-square-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> FAQs </span>
                </a>
            </li>
            @endcan

            <li class="menu-title mt-2">Settings</li>

            @can('settings.view')
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ Request::routeIs('admin.settings.*') ? '' : 'collapsed' }}" href="#sidebarSettings" data-bs-toggle="collapse" role="button" aria-expanded="{{ Request::routeIs('admin.settings.*') ? 'true' : 'false' }}" aria-controls="sidebarSettings">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Settings </span>
                </a>
                <div class="collapse {{ Request::routeIs('admin.settings.*') ? 'show' : '' }}" id="sidebarSettings">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.settings.general') ? 'active' : '' }}" href="{{ route('admin.settings.general') }}">General Settings</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.settings.contact') ? 'active' : '' }}" href="{{ route('admin.settings.contact') }}">Contact Settings</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ Request::routeIs('admin.settings.policies.edit') ? 'active' : '' }}" href="{{ route('admin.settings.policies.edit') }}">Policy Settings</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

        </ul>
    </div>
</div>
<!-- ========== App Menu End ========== -->
