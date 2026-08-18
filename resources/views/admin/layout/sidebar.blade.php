<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link brand-link-pn d-flex align-items-center px-3">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="sidebar-logo-img">
        <span class="brand-text font-weight-bold ml-2 sidebar-brand-txt">
            PAK NAMAK<br><small class="sidebar-brand-sub">& MASALA JAAT</small>
        </span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            @php
                $shopsActive     = request()->routeIs('admin.shops.*');
                $vendorsActive   = request()->routeIs('admin.vendors.*');
                $employeesActive = request()->routeIs('admin.employees.*') || request()->routeIs('admin.holidays.*');

                $ordersActive = request()->routeIs('admin.orders.*') || request()->routeIs('admin.spice-orders.*');
                $overviewCatActive = request()->routeIs('dashboard') || request()->routeIs('admin.cash_ledger.*') || $ordersActive;
                $salesCatActive    = request()->routeIs('admin.sales.*') || $shopsActive;
                $purchCatActive    = request()->routeIs('admin.purchases.*') || $vendorsActive || request()->routeIs('admin.productions.*') || request()->routeIs('admin.stocks.*');
                $financeCatActive  = request()->routeIs('admin.expenses.*') || request()->routeIs('admin.assets.*');
                $settingsCatActive = request()->routeIs('admin.types.*') || request()->routeIs('admin.cities.*') || request()->routeIs('admin.areas.*');
                $spicesCatActive   = request()->routeIs('admin.spice-*');
            @endphp
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">

                {{-- ═══ OVERVIEW ═══ --}}
                <li class="nav-item has-treeview {{ $overviewCatActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link nav-cat-link {{ $overviewCatActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-gauge-high"></i>
                        <p>Overview <small class="d-block nav-sub-lbl">جائزہ</small> <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dashboard <small class="d-block nav-sub-lbl">ڈیش بورڈ</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.cash_ledger.index') }}" class="nav-link {{ request()->routeIs('admin.cash_ledger.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cash &amp; Bank <small class="d-block nav-sub-lbl">نقد اور بینک</small></p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview {{ $ordersActive ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $ordersActive ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Orders <small class="d-block nav-sub-lbl">آرڈرز</small>
                                    @php
                                        $pendingOrders = \App\Models\Order::where('status','pending')->count();
                                        $pendingSpiceOrders = \App\Models\SpiceOrder::where('status','pending')->count();
                                    @endphp
                                    @if(($pendingOrders + $pendingSpiceOrders) > 0)
                                        <span class="badge badge-warning right icon-10">{{ $pendingOrders + $pendingSpiceOrders }}</span>
                                    @endif
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.orders.index') }}"
                                       class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            Salt <small class="d-block nav-sub-lbl">نمک</small>
                                            @if($pendingOrders > 0)
                                                <span class="badge badge-warning right icon-10">{{ $pendingOrders }}</span>
                                            @endif
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.spice-orders.index') }}"
                                       class="nav-link {{ request()->routeIs('admin.spice-orders.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            Chilli <small class="d-block nav-sub-lbl">مرچ</small>
                                            @if($pendingSpiceOrders > 0)
                                                <span class="badge badge-warning right icon-10">{{ $pendingSpiceOrders }}</span>
                                            @endif
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                {{-- ═══ SALES ═══ --}}
                <li class="nav-item has-treeview {{ $salesCatActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link nav-cat-link {{ $salesCatActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
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

                        {{-- Shops sub-menu --}}
                        <li class="nav-item has-treeview {{ $shopsActive ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $shopsActive ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Shops <small class="d-block nav-sub-lbl">دکانیں</small> <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.shops.index') }}"
                                       class="nav-link {{ request()->routeIs('admin.shops.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>All Shops <small class="d-block nav-sub-lbl">تمام دکانیں</small></p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.shops.payment_form') }}"
                                       class="nav-link {{ request()->routeIs('admin.shops.payment_form') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Record Payment <small class="d-block nav-sub-lbl">ادائیگی درج کریں</small></p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                {{-- ═══ PROCUREMENT ═══ --}}
                <li class="nav-item has-treeview {{ $purchCatActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link nav-cat-link {{ $purchCatActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-dolly"></i>
                        <p>Procurement <small class="d-block nav-sub-lbl">خریداری اور پیداوار</small> <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.purchases.index') }}" class="nav-link {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Purchases <small class="d-block nav-sub-lbl">خریداری</small></p>
                            </a>
                        </li>

                        <li class="nav-item has-treeview {{ $vendorsActive ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $vendorsActive ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Vendors <small class="d-block nav-sub-lbl">فروش کار</small> <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.vendors.index') }}"
                                       class="nav-link {{ request()->routeIs('admin.vendors.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>All Vendors <small class="d-block nav-sub-lbl">تمام فروش کار</small></p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.vendors.payment_form') }}"
                                       class="nav-link {{ request()->routeIs('admin.vendors.payment_form') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Record Payment <small class="d-block nav-sub-lbl">ادائیگی درج کریں</small></p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.vendors.advance_form') }}"
                                       class="nav-link {{ request()->routeIs('admin.vendors.advance_form') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Send Advance <small class="d-block nav-sub-lbl">ایڈوانس بھیجیں</small></p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.productions.index') }}" class="nav-link {{ request()->routeIs('admin.productions.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Production <small class="d-block nav-sub-lbl">پیداوار</small></p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.stocks.index') }}" class="nav-link {{ request()->routeIs('admin.stocks.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Stock <small class="d-block nav-sub-lbl">اسٹاک</small></p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ═══ SPICES ═══ --}}
                <li class="nav-item has-treeview {{ $spicesCatActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link nav-cat-link {{ $spicesCatActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-pepper-hot"></i>
                        <p>Spices <small class="d-block nav-sub-lbl">مصالحہ جات</small> <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.spice-sales.index') }}" class="nav-link {{ request()->routeIs('admin.spice-sales.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Spice Sales <small class="d-block nav-sub-lbl">مصالحہ فروخت</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.spice-purchases.index') }}" class="nav-link {{ request()->routeIs('admin.spice-purchases.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Spice Purchases <small class="d-block nav-sub-lbl">مصالحہ خریداری</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.spice-orders.index') }}" class="nav-link {{ request()->routeIs('admin.spice-orders.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Spice Orders <small class="d-block nav-sub-lbl">مصالحہ آرڈرز</small>
                                    @php $pendingSpiceOrders = \App\Models\SpiceOrder::where('status','pending')->count(); @endphp
                                    @if($pendingSpiceOrders > 0)
                                        <span class="badge badge-warning right icon-10">{{ $pendingSpiceOrders }}</span>
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.spice-stock.index') }}" class="nav-link {{ request()->routeIs('admin.spice-stock.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Spice Stock <small class="d-block nav-sub-lbl">مصالحہ اسٹاک</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.spice-types.index') }}" class="nav-link {{ request()->routeIs('admin.spice-types.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Spice Types <small class="d-block nav-sub-lbl">مصالحہ کی اقسام</small></p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ═══ FINANCE ═══ --}}
                <li class="nav-item has-treeview {{ $financeCatActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link nav-cat-link {{ $financeCatActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-sack-dollar"></i>
                        <p>Finance <small class="d-block nav-sub-lbl">مالیات</small> <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.expenses.index') }}" class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Expenses <small class="d-block nav-sub-lbl">اخراجات</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.assets.index') }}" class="nav-link {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Assets <small class="d-block nav-sub-lbl">اثاثے</small></p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ═══ HR ═══ --}}
                <li class="nav-item has-treeview {{ $employeesActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link nav-cat-link {{ $employeesActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>HR <small class="d-block nav-sub-lbl">انسانی وسائل</small> <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.employees.index') }}"
                               class="nav-link {{ request()->routeIs('admin.employees.index') || request()->routeIs('admin.employees.show') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Employees <small class="d-block nav-sub-lbl">تمام ملازمین</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.employees.advance_form') }}"
                               class="nav-link {{ request()->routeIs('admin.employees.advance_form') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Record Advance <small class="d-block nav-sub-lbl">ایڈوانس درج کریں</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.holidays.index') }}"
                               class="nav-link {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Holidays <small class="d-block nav-sub-lbl">تعطیلات</small></p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ═══ SETTINGS ═══ --}}
                <li class="nav-item has-treeview {{ $settingsCatActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link nav-cat-link {{ $settingsCatActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-gear"></i>
                        <p>Settings <small class="d-block nav-sub-lbl">ترتیبات</small> <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.types.index') }}" class="nav-link {{ request()->routeIs('admin.types.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Salt Types <small class="d-block nav-sub-lbl">نمک کی اقسام</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.cities.index') }}" class="nav-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cities <small class="d-block nav-sub-lbl">شہر</small></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.areas.index') }}" class="nav-link {{ request()->routeIs('admin.areas.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Areas <small class="d-block nav-sub-lbl">علاقے</small></p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>
