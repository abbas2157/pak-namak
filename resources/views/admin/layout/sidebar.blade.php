<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        <span class="brand-text font-weight-light">PN&MJ</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-inbox"></i>
                        <p>
                            Orders
                            @php $pendingOrders = \App\Models\Order::where('status','pending')->count(); @endphp
                            @if($pendingOrders > 0)
                                <span class="badge badge-warning right" style="font-size:10px;">{{ $pendingOrders }}</span>
                            @endif
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.purchases.index') }}" class="nav-link {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cart-shopping"></i>
                        <p>Purchases</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.productions.index') }}" class="nav-link {{ request()->routeIs('admin.productions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-industry"></i>
                        <p>Production</p>
                    </a>
                </li>

                {{-- Sales grouped sub-menu --}}
                @php $salesActive = request()->routeIs('admin.sales.*') || request()->routeIs('admin.sales.by_shop') || request()->routeIs('admin.sales.report'); @endphp
                <li class="nav-item has-treeview {{ $salesActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $salesActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>Sales <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.sales.index') }}"
                               class="nav-link {{ request()->routeIs('admin.sales.index') || request()->routeIs('admin.sales.create') || request()->routeIs('admin.sales.store') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Sales</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.sales.by_shop') }}"
                               class="nav-link {{ request()->routeIs('admin.sales.by_shop') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>By Shop</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.sales.report') }}"
                               class="nav-link {{ request()->routeIs('admin.sales.report*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sales Report</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.shops.index') }}" class="nav-link {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shop"></i>
                        <p>Shops</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.vendors.index') }}" class="nav-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes-packing"></i>
                        <p>Vendors / Supplier</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Employees</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.expenses.index') }}" class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Expenses</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.assets.index') }}" class="nav-link {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Assets</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.types.index') }}" class="nav-link {{ request()->routeIs('admin.types.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bars"></i>
                        <p>Salt Types</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
