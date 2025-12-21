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
                    <a href="{{ route('admin.types.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'admin.types') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Title</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.vendors.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'admin.vendors') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Vendors</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.package.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'admin.package') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Packages</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.employees.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'admin.employee') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Employees</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.assets.index') }}"
                        class="nav-link {{ Str::startsWith(Request::route()->getName(), 'admin.assets') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Assets</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.salt-purchases.index') }}"
                    class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.salt-purchases') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Salt Purchases</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.productions.index') }}"
                        class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.production') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Production</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
