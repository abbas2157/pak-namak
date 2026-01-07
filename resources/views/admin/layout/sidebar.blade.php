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
                    <a href="{{ route('salt-types.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'salt-types') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bars"></i>
                        <p>Salt Types</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('vendors.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'vendors') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Vendors</p>
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
                <li class="nav-item">
                    <a href="{{ route('salt-purchases.index') }}"
                    class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'salt-purchases') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Salt Purchases</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('productions.index') }}"
                        class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'production') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Production</p>
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
                    <a href="{{ route('sales.index') }}"
                        class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'sales') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Sales</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
