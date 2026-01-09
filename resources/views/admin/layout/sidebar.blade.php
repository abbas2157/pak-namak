<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('home') }}" class="brand-link text-center">
        <span class="brand-text font-weight-light">PN&MJ</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.purchases.index') }}" class="nav-link {{ request()->is('admin/purchases') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Purchases</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.productions.index') }}" class="nav-link {{ request()->is('admin/productions') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Production</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.sales.index') }}" class="nav-link {{ request()->is('admin/sales') || request()->is('admin/sales/create') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Sales</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('shops.index') }}"
                        class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'shops') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Shops</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('salt-types.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'salt-types') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bars"></i>
                        <p>Salt Types</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.vendors.index') }}"
                        class="nav-link {{ (request()->is('admin/vendors') || request()->is('admin/vendors/create')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes-packing"></i>
                        <p>Vendors/Supplier</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('package.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'package') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Packages</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'employee') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Employees</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('assets.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'assets') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Assets</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
