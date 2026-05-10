# PAK NAMAK — CLAUDE.md

## Project Overview

**PAK NAMAK & MASALA JAAT PRIVATE LIMITED** — a business operations management system for a salt trading and production company. Tracks sales, purchases, production, expenses, employees, assets, and shops.

- **App URL**: `http://localhost/pak-namak/public` (Laragon, local)
- **Database**: MySQL, db name `paknamak`
- **Admin panel**: `/admin/` (requires auth)

## Tech Stack

- **Laravel 12** (PHP 8.2+)
- **MySQL** via Laragon
- **Blade templates** — server-side rendering, no separate frontend framework
- **Tailwind CSS v4** — compiled via Vite 7
- **Vite 7** — asset bundling (`npm run dev` / `npm run build`)
- **Axios** — AJAX calls within Blade views

## Key Commands

```bash
# Start all dev services (server + queue + logs + Vite)
composer dev

# Run tests
composer test

# Vite dev server only
npm run dev

# Production build
npm run build

# Code formatting
./vendor/bin/pint
```

## Directory Structure

```
app/Http/Controllers/Admin/   # All admin controllers (16 total)
app/Models/                   # Eloquent models (16 total)
resources/views/admin/        # Blade templates
  layout/                     # app, header, sidebar, footer
  dashboard/
  sales/
  purchases/
  productions/
  shops/
  vendors/
  employees/
  expenses/
  assets/
  types/
routes/
  web.php                     # Welcome page only
  admin.php                   # All admin routes (auth-gated)
database/migrations/          # 19 migrations
config/admin.php              # Business config (shop name, phone, packages)
```

## Domain Model

### Sales (most complex feature)
A single sale (`sales` table) can have three types of line items:

| Type | Table | Unit | Notes |
|---|---|---|---|
| Dalla | `sale_dallas` | Mann (1 Mann = 40 kg) | Bulk salt |
| Thalia | `sale_thailas` | Bags (5/10/50 kg) | Bagged salt |
| Package | `sale_packages` | Bundles (200g–800g packs) | Retail packages |

Sales track `received_amount` and `pending_amount` (credit/udhaar system). Bill images can be uploaded.

### Core Tables
- `shops` — retail outlets (buyers)
- `vendors` — salt suppliers
- `salt_types` — product categories
- `salt_purchases` — raw material inbound
- `productions` — manufacturing records (raw → finished, wastage)
- `employees`, `assets`, `expenses` — operational records
- `sale_payments` — payment installments per sale

## Controllers

All controllers live under `app/Http/Controllers/Admin/`.

| Controller | Route Prefix |
|---|---|
| DashboardController | `/admin/` |
| SaleController | `/admin/sales` |
| PurchaseController | `/admin/purchases` |
| ProductionController | `/admin/productions` |
| ShopController | `/admin/shops` |
| VendorController | `/admin/vendors` |
| EmployeeController | `/admin/employees` |
| ExpenseController | `/admin/expenses` |
| AssetController | `/admin/assets` |
| TypeController | `/admin/types` |
| SalesReportController | `/admin/sales-report` |
| ShopSalesController | `/admin/sales-by-shop` |
| SaleReceiptController | `/admin/sales/{id}/receipt` |
| AuthController | `/admin/login` |

## Important Patterns

- **DB transactions** — `SaleController` wraps sale creation in `DB::beginTransaction()`. Follow this for any write that touches multiple tables.
- **JSON responses** — controllers return JSON for AJAX modal operations (purchases, expenses use this pattern).
- **Eager loading** — use `with()` when loading sales with line items to avoid N+1.
- **config/admin.php** — package sizes (200g–800g) and bundle sizes (10/20 packs) are defined here, not hardcoded in views.
- **File uploads** — bill images stored under `storage/app/public/bills/`, served via `storage:link`.

## Business Rules

- 1 Mann = 40 kg (local unit for bulk salt)
- `grand_total` on purchases = `total_cost + transport_cost + loading_unloading_cost`
- `pending_amount` = `total_amount - received_amount` (tracked per sale)
- Dashboard profit = total sales - purchases - expenses - production costs
- Dashboard supports month-level filtering (Oct 2025 onwards)

## Authentication

- Single user model (`users` table)
- Laravel `auth` middleware gates all `/admin/*` routes
- No role/permission system — single admin user
- Login: `/admin/login`
