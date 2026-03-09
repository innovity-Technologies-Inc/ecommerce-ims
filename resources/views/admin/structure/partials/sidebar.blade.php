<!-- ========== App Menu Start ========== -->
@php($gs = \App\HelperClass::generalSettings())
<div class="main-nav">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="{{ route('admin.dashboard') }}" class="logo-dark">
            <img src="{{ $gs->favicon ? asset('storage/'.$gs->favicon) : asset('admin/assets/images/favicon.ico') }}" class="logo-sm" alt="logo sm" style="height: 30px;">
            <img src="{{ $gs->dark_logo ? asset('storage/'.$gs->dark_logo) : asset('admin/assets/images/logo-dark.png') }}" class="logo-lg" alt="logo dark" style="height: 50px;">
        </a>

        <a href="{{ route('admin.dashboard') }}" class="logo-light">
            <img src="{{ $gs->favicon ? asset('storage/'.$gs->favicon) : asset('admin/assets/images/favicon.ico') }}" class="logo-sm" alt="logo sm" style="height: 30px;">
            <img src="{{ $gs->dark_logo ? asset('storage/'.$gs->dark_logo) : asset('admin/assets/images/logo-light.png') }}" class="logo-lg" alt="logo light" style="height: 50px;">
        </a>
    </div>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">General</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProducts">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:t-shirt-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Products </span>
                </a>
                <div class="collapse" id="sidebarProducts">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.products.index') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.products.create') }}">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarCategory" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCategory">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Category </span>
                </a>
                <div class="collapse" id="sidebarCategory">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.categories.index') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.categories.create') }}">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarBrand" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarBrand">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:tag-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Brands </span>
                </a>
                <div class="collapse" id="sidebarBrand">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.brands.index') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.brands.create') }}">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarHomepage" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarHomepage">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Homepage </span>
                </a>
                <div class="collapse" id="sidebarHomepage">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.sliders.index') }}">Sliders</a>
                        </li>
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
                    </ul>
                </div>
            </li>

            <li class="menu-title mt-2">Users</li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarUsers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsers">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:user-speak-bold-duotone"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Users </span>
                </a>
                <div class="collapse" id="sidebarUsers">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.index') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.create') }}">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-title mt-2">Settings</li>

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
                            <a class="sub-nav-link" href="{{ route('admin.settings.mail') }}">Mail Settings</a>
                        </li>
                    </ul>
                </div>
            </li>

        </ul>
    </div>
</div>
<!-- ========== App Menu End ========== -->
