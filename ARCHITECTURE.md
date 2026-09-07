# PAK NAMAK — Architecture

This document describes how the codebase is actually built today, not an aspirational target. It exists so new code can follow the grain of the app instead of inventing a new pattern per feature, and so architecture adherence can be scored (see `.claude/agents/architecture-score.md`).

For business domain vocabulary (Dalla/Thaila/Package units, Mann, `pending_amount`, etc.) see `CLAUDE.md` — this document is about structure and conventions, not the business.

## 1. Overview

PAK NAMAK is a single-admin, server-rendered internal ops tool — not an API product. The layering is intentionally shallow:

```
routes/admin.php  →  Controller (Admin namespace)  →  Eloquent Model  →  Blade view
```

There is **no service layer, no repository layer, no API resource/DTO layer**. Controllers hold validation, DB transaction boundaries, querying, and response shaping directly. This is a deliberate trade-off for a small internal tool with one developer and one admin user — it keeps each feature's logic in one file. It does mean controllers grow large (e.g. `SaleController`, `PurchaseController`); that is accepted, not a bug to fix opportunistically.

Do not introduce a service/repository layer for a single feature — it would be inconsistent with every other controller and make the codebase harder to read as a whole, not easier.

## 2. Module map

| Domain | Product-line specific | Shared across product lines |
|---|---|---|
| Product/catalog | `salt_types`, `spice_types` | — |
| Purchasing | `purchases`+`purchase_payments`, `spice_purchases`+`spice_purchase_payments` | `vendors`, `vendor_advances`, `accounts`, `cash_ledger` |
| Selling | `sales`+`sale_dallas`/`sale_thailas`/`sale_packages`+`sale_payments`, `spice_sales`+`spice_sale_items`+`spice_sale_payments` | `shops`, `cities`, `areas`, `accounts`, `cash_ledger` |
| Public order intake | `orders`+`order_items`, `spice_orders`+`spice_order_items` | — |
| Stock | `stocks`+`stock_movements`, `spice_stocks`+`spice_stock_movements` | — |
| Money | — | `accounts`, `cash_ledger`, `account_transfers` |
| Operations | `productions` (salt only — no spice equivalent) | `employees`, `employee_salaries`, `employee_absences`, `company_holidays`, `expenses`, `assets`, investments (flag on assets/expenses/purchases) |

**Salt and Spices are deliberately separate parallel modules**, not one schema with a product-type column. Spices (`Spice*` controllers/models, `spice_*` tables, `resources/views/admin/spice-*`) mirrors Salt's shape 1:1 but package-only (no Dalla/Thaila bulk or bagged-kg equivalent, no bundle concept — spices sell as individual packets). This was an explicit decision to avoid regression risk on working Salt code and because the product lines are structurally different (different units, no bundling for spices).

**Rule**: if a genuinely new product line is added, default to another fully separate module following the Spices pattern, rather than adding a `product_type`/`product_id` column to Salt's tables. Only extend a *shared* entity (Shops, Vendors, Accounts, CashLedger, Cities/Areas) when the new thing is truly business-entity-shared, not product-specific.

## 3. Controller conventions

- One controller per resource, under `app/Http/Controllers/Admin/`, extending the plain `Controller` base.
- Standard CRUD goes through `Route::resource($uri, Controller::class, ['as' => 'admin'])` in `routes/admin.php`; anything beyond CRUD (payments, advances, quick-update, previews, PDF export) is a named extra route pointing at a public method on the same controller — not a separate controller.
- **Kebab multi-word resource gotcha**: `Route::resource('spice-sales', ...)` / `Route::resource('spice-types', ...)` auto-generate snake_case wildcards (`{spice_sale}`, `{spice_type}`), which silently fail to bind against camelCase controller parameters (`$spiceSale`) and inject an empty unsaved model instead of erroring. Any new multi-word-kebab resource **must** pin its wildcard explicitly: `->parameters(['spice-sales' => 'spiceSale'])`.
- **Validation is mixed, and that's known, current state — not a target to normalize away**: 28 of 30 controllers call `$request->validate([...])` inline at the top of `store`/`update`. Only two extractions exist: `App\Http\Requests\SaleRequest` and `ProductionRequest`. Don't flag inline validation as a violation; only flag it if a new controller duplicates validation logic across multiple methods where a Form Request would obviously help (matching the reason Sale/Production got one).
- Multi-table writes (a purchase + its payment row, a sale + its line items) are wrapped in `DB::transaction()`. Follow this for any new write that touches more than one table together.
- List/index actions build filter options (month dropdowns via `selectRaw("DATE_FORMAT(...)")`, active-account lists) and pass everything to the view via `compact()` — no view models.

## 4. Data & money-movement conventions

- **Derive, don't duplicate.** Running totals like `pending_amount` (per sale/purchase) and account balances are always computed (`total - received`, `SUM(in) - SUM(out)`), never stored as a maintained cache column.
- **CashLedger is the single source of truth for cash movement.** Money-affecting models — `SalePayment`, `PurchasePayment`, `Expense`, `EmployeeSalary`, and their Spice equivalents (`SpiceSalePayment`, `SpicePurchasePayment`) — sync into `cash_ledger` via a `booted()` model hook calling `CashLedger::sync($sourceType, $sourceId, $type, $amount, $date, $description, $accountId, $isInvestment)` on `created`/`updated`, and `CashLedger::remove($sourceType, $sourceId)` on `deleted` (see `app/Models/Expense.php` for the canonical shape).
- Every ledger-affecting record is tagged to an `Account` (`account_id`) — Cash in Hand, JazzCash, EasyPaisa, or a named bank account. A null `account_id` means "not yet tied to a ledger entry" and the sync hook removes any stale ledger row for it.
- Opening balance is a normal `cash_ledger` row with `source_type = 'opening_balance'`, keyed per account (`source_id = account_id`), set explicitly per account rather than inferred.
- Investment-flagged records (`is_investment` on `assets`/`expenses`/`purchases`) still move cash normally through the same sync hook, just carrying the flag through to the ledger row so dashboard totals can include/exclude them.
- **Rule for any new money-affecting feature**: extend via the existing `booted()` + `CashLedger::sync()` pattern and `account_id` tagging. Do not build a parallel balance-tracking mechanism.

## 5. Config-driven business constants

`config/admin.php` is the source of truth for business-configurable scales: package sizes (`packages`), thaila sizes (`thaila_sizes`), spice weight scale (`spice_sizes`), bundle sizes (`bundles`), and the weekly holiday day (`weekly_holiday`). Controllers and views read these via `config('admin.*')` rather than hardcoding size/bundle literals. A new size or bundle option is a config change, not a migration or code change.

## 6. View layer

Plain Blade under `resources/views/admin/<feature>/`, populated via `compact()` from the controller — no view models, no presenters, no separate API resource/serializer layer (there is no JSON API surface beyond a few AJAX modal endpoints that return JSON directly from the controller). Shared chrome lives in `resources/views/admin/layout/` (`app`, `header`, `sidebar`, `footer`).

## 7. Migrations

Additive, timestamp-ordered migrations (`add_<field>_to_<table>_table`) for incremental schema changes rather than editing already-shipped migrations. This lets the migration history double as a changelog of feature rollout order (e.g. `add_investment_flag_and_account_to_assets_expenses_purchases` documents when investment tracking landed).

## 8. Known deviations / debt

These are pre-existing, accepted as of this document — don't re-report them as newly-discovered problems, only flag *new* instances of the same pattern:

- Inline `$request->validate()` in 28/30 controllers vs. Form Request classes in 2 (`SaleRequest`, `ProductionRequest`) — see §3.
- Duplicate/near-duplicate shop-sales views: `resources/views/admin/shops/sales.blade.php` and `sales2.blade.php` both exist.
- No automated test coverage for controllers/models beyond whatever ships in `tests/` — `composer test` should still be run, but architecture scoring does not currently grade test coverage.
