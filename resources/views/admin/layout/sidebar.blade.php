<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
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
                        <i class="nav-icon fas fa-cart-shopping"></i>
                        <p>Purchases</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.productions.index') }}" class="nav-link {{ request()->is('admin/productions') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-umbrella-beach"></i>
                        <p>Production</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.sales.index') }}" class="nav-link {{ request()->is('admin/sales') || request()->is('admin/sales/create') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>Sales</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.shops.index') }}"
                        class="nav-link {{ request()->is('admin/shops') || request()->is('admin/shops/create') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shop"></i>
                        <p>Shops</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.vendors.index') }}" class="nav-link {{ (request()->is('admin/vendors') || request()->is('admin/vendors/create')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes-packing"></i>
                        <p>Vendors/Supplier</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.employees.index') }}" class="nav-link {{ (request()->is('admin/employees') || request()->is('admin/employees/create')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Employees</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.types.index') }}"
                        class="nav-link {{ request()->is('admin/types') || request()->is('admin/types/create') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bars"></i>
                        <p>Types</p>
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
