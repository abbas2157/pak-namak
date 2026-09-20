<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_expenses_index_filters_by_date_category_account_and_type(): void
    {
        $user = User::factory()->create();
        $cash = Account::create(['name' => 'Cash', 'type' => 'cash', 'is_active' => true]);
        $bank = Account::create(['name' => 'Bank', 'type' => 'bank', 'is_active' => true]);

        $mk = fn ($date, $cat, $amt, $acc, $inv = false) => Expense::create([
            'expense_date' => $date, 'category' => $cat, 'payment_method' => 'cash', 'amount' => $amt,
            'account_id' => $acc->id, 'is_investment' => $inv,
        ]);
        $mk('2026-09-02', 'Fuel', 500, $cash);
        $mk('2026-09-15', 'Rent', 20000, $bank);
        $mk('2026-08-20', 'Fuel', 700, $cash, true);

        $get = fn ($q) => $this->actingAs($user)->get(route('admin.expenses.index', $q))->assertOk();

        $get([])->assertViewHas('grandTotal', 21200.0)->assertSee('All Categories');
        $get(['category' => 'Fuel'])->assertViewHas('grandTotal', 1200.0);
        $get(['from' => '2026-09-01', 'to' => '2026-09-30'])->assertViewHas('grandTotal', 20500.0);
        $get(['account_id' => $bank->id])->assertViewHas('grandTotal', 20000.0);
        $get(['type' => 'investment'])->assertViewHas('grandTotal', 700.0);
        $get(['type' => 'operating', 'category' => 'Fuel'])->assertViewHas('grandTotal', 500.0);
        $get(['category' => 'Fuel', 'from' => '2026-09-01'])->assertViewHas('grandTotal', 500.0)->assertSee('Clear all filters');
    }
}
