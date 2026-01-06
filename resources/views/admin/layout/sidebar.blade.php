<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('home') }}" class="brand-link">
        <span class="brand-text font-weight-light">PakNamak</span>
    </a>
    <div class="sidebar">
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('salt-types.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'salt-types') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Title</p>
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
