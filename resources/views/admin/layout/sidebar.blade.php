<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link brand-link-pn d-flex align-items-center px-3">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="sidebar-logo-img">
        <span class="brand-text font-weight-bold ml-2 sidebar-brand-txt">
            PAK NAMAK<br><small class="sidebar-brand-sub">& MASALA JAAT</small>
        </span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard <small class="d-block nav-sub-lbl">ڈیش بورڈ</small></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-inbox"></i>
                        <p>
                            Orders <small class="d-block nav-sub-lbl">آرڈرز</small>
                            @php $pendingOrders = \App\Models\Order::where('status','pending')->count(); @endphp
                            @if($pendingOrders > 0)
                                <span class="badge badge-warning right icon-10">{{ $pendingOrders }}</span>
                            @endif
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.purchases.index') }}" class="nav-link {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cart-shopping"></i>
                        <p>Purchases <small class="d-block nav-sub-lbl">خریداری</small></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.productions.index') }}" class="nav-link {{ request()->routeIs('admin.productions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-industry"></i>
                        <p>Production <small class="d-block nav-sub-lbl">پیداوار</small></p>
                    </a>
                </li>

                {{-- Sales grouped sub-menu --}}
                @php $salesActive = request()->routeIs('admin.sales.*') || request()->routeIs('admin.sales.by_shop') || request()->routeIs('admin.sales.report'); @endphp
                <li class="nav-item has-treeview {{ $salesActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $salesActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>Sales <small class="d-block nav-sub-lbl">فروخت</small> <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.sales.index') }}"
                               class="nav-link {{ request()->routeIs('admin.sales.index') || request()->routeIs('admin.sales.create') || request()->routeIs('admin.sales.store') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Sales <small class="d-block nav-sub-lbl">تمام فروخت</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.sales.by_shop') }}"
                               class="nav-link {{ request()->routeIs('admin.sales.by_shop') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>By Shop <small class="d-block nav-sub-lbl">دکان کے مطابق</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.sales.report') }}"
                               class="nav-link {{ request()->routeIs('admin.sales.report*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sales Report <small class="d-block nav-sub-lbl">فروخت رپورٹ</small></p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Shops + Cities sub-menu --}}
                @php $shopsActive = request()->routeIs('admin.shops.*') || request()->routeIs('admin.cities.*') || request()->routeIs('admin.areas.*'); @endphp
                <li class="nav-item has-treeview {{ $shopsActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $shopsActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shop"></i>
                        <p>Shops <small class="d-block nav-sub-lbl">دکانیں</small> <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.shops.index') }}"
                               class="nav-link {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Shops <small class="d-block nav-sub-lbl">تمام دکانیں</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.cities.index') }}"
                               class="nav-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cities <small class="d-block nav-sub-lbl">شہر</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.areas.index') }}"
                               class="nav-link {{ request()->routeIs('admin.areas.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Areas <small class="d-block nav-sub-lbl">علاقے</small></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.vendors.index') }}" class="nav-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes-packing"></i>
                        <p>Vendors / Supplier <small class="d-block nav-sub-lbl">فروش کار / سپلائر</small></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Employees <small class="d-block nav-sub-lbl">ملازمین</small></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.expenses.index') }}" class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Expenses <small class="d-block nav-sub-lbl">اخراجات</small></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.assets.index') }}" class="nav-link {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Assets <small class="d-block nav-sub-lbl">اثاثے</small></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.types.index') }}" class="nav-link {{ request()->routeIs('admin.types.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bars"></i>
                        <p>Salt Types <small class="d-block nav-sub-lbl">نمک کی اقسام</small></p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
