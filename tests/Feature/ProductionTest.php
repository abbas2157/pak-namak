<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\CashLedger;
use App\Models\Production;
use App\Models\SpiceProduction;
use App\Models\SpiceStock;
use App\Models\SpiceStockMovement;
use App\Models\SpiceType;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function stockQty(string $type, ?int $size, ?int $bundle = null): float
    {
        return (float) (Stock::where('product_type', $type)->where('size', $size)->where('bundle_size', $bundle)->value('quantity') ?? 0);
    }

    public function test_salt_production_records_thaila_and_packages_and_posts_to_stock(): void
    {
        $res = $this->actingAs($this->admin())->postJson(route('admin.productions.store'), [
            'production_date' => '2026-09-19',
            'raw_salt_used' => 1000,
            'finished_salt' => 950,
            'wastage' => 50,
            'thaila' => [10 => 30, 50 => 5],
            'package' => [500 => [10 => 20, 20 => 4]],
        ]);

        $res->assertOk()->assertJson(['success' => true]);

        $production = Production::with('items')->first();
        $this->assertCount(4, $production->items);
        $this->assertSame(35.0, $production->thailaCount());
        $this->assertSame(24.0, $production->packageCount());
        // 30×10 + 5×50 + 20×(0.5×10) + 4×(0.5×20) = 300 + 250 + 100 + 40
        $this->assertSame(690.0, $production->packedKg());

        $this->assertSame(30.0, $this->stockQty('thaila', 10));
        $this->assertSame(5.0, $this->stockQty('thaila', 50));
        $this->assertSame(20.0, $this->stockQty('package', 500, 10));
        $this->assertSame(4.0, $this->stockQty('package', 500, 20));
        $this->assertSame(4, StockMovement::where('reason', 'production')->count());
    }

    public function test_editing_salt_production_replaces_items_without_double_counting_stock(): void
    {
        $user = $this->admin();
        $this->actingAs($user)->postJson(route('admin.productions.store'), [
            'production_date' => '2026-09-19',
            'raw_salt_used' => 100,
            'finished_salt' => 100,
            'thaila' => [10 => 30],
        ])->assertOk();

        $production = Production::first();

        $this->actingAs($user)->putJson(route('admin.productions.update', $production), [
            'production_date' => '2026-09-19',
            'raw_salt_used' => 100,
            'finished_salt' => 100,
            'thaila' => [10 => 12, 5 => 8],
        ])->assertOk();

        $this->assertSame(12.0, $this->stockQty('thaila', 10));
        $this->assertSame(8.0, $this->stockQty('thaila', 5));
        $this->assertCount(2, $production->fresh()->items);
        $this->assertSame(2, StockMovement::where('reason', 'production')->count());
    }

    public function test_deleting_salt_production_reverses_stock_and_ledger(): void
    {
        $user = $this->admin();
        $account = Account::create(['name' => 'Cash', 'type' => 'cash', 'is_active' => true]);

        $this->actingAs($user)->postJson(route('admin.productions.store'), [
            'production_date' => '2026-09-19',
            'raw_salt_used' => 100,
            'finished_salt' => 100,
            'electricity_fuel_cost' => 500,
            'account_id' => $account->id,
            'thaila' => [10 => 30],
        ])->assertOk();

        $production = Production::first();
        $this->assertSame(1, CashLedger::where('source_type', 'production')->count());

        $this->actingAs($user)->deleteJson(route('admin.productions.destroy', $production))->assertOk();

        $this->assertSame(0.0, $this->stockQty('thaila', 10));
        $this->assertSame(0, StockMovement::count());
        $this->assertSame(0, CashLedger::where('source_type', 'production')->count());
        $this->assertDatabaseCount('production_items', 0);
    }

    public function test_production_stock_movements_cannot_be_deleted_from_stock_page(): void
    {
        $user = $this->admin();
        $this->actingAs($user)->postJson(route('admin.productions.store'), [
            'production_date' => '2026-09-19',
            'raw_salt_used' => 100,
            'finished_salt' => 100,
            'thaila' => [10 => 30],
        ])->assertOk();

        $movement = StockMovement::first();
        $this->actingAs($user)->deleteJson(route('admin.stocks.movements.destroy', $movement))->assertStatus(422);
        $this->assertSame(30.0, $this->stockQty('thaila', 10));
    }

    public function test_spice_production_is_separate_and_posts_packets_to_spice_stock(): void
    {
        $user = $this->admin();
        $spice = SpiceType::first() ?? SpiceType::create(['title' => 'Chilli Powder']);

        $this->actingAs($user)->postJson(route('admin.spice-productions.store'), [
            'spice_type_id' => $spice->id,
            'production_date' => '2026-09-19',
            'raw_spice_used' => 50,
            'finished_spice' => 48,
            'package' => [100 => 200, 1000 => 10],
        ])->assertOk()->assertJson(['success' => true]);

        $production = SpiceProduction::with('items')->first();
        $this->assertSame(210.0, $production->packetCount());
        $this->assertSame(30.0, $production->packedKg()); // 200×0.1 + 10×1

        $this->assertSame(200.0, (float) SpiceStock::where('spice_type_id', $spice->id)->where('size', 100)->value('quantity'));
        $this->assertSame(10.0, (float) SpiceStock::where('spice_type_id', $spice->id)->where('size', 1000)->value('quantity'));

        // Salt stock untouched — the modules are separate
        $this->assertSame(0, Stock::count());
        $this->assertSame(0, Production::count());

        $this->actingAs($user)->deleteJson(route('admin.spice-productions.destroy', $production))->assertOk();
        $this->assertSame(0, SpiceStockMovement::count());
        $this->assertSame(0.0, (float) SpiceStock::where('spice_type_id', $spice->id)->where('size', 100)->value('quantity'));
    }

    public function test_index_pages_render_with_daily_summary(): void
    {
        $user = $this->admin();
        $spice = SpiceType::first() ?? SpiceType::create(['title' => 'Chilli Powder']);

        $this->actingAs($user)->postJson(route('admin.productions.store'), [
            'production_date' => now()->toDateString(),
            'raw_salt_used' => 100,
            'finished_salt' => 100,
            'thaila' => [10 => 7],
        ])->assertOk();

        $this->actingAs($user)->postJson(route('admin.spice-productions.store'), [
            'spice_type_id' => $spice->id,
            'production_date' => now()->toDateString(),
            'raw_spice_used' => 10,
            'finished_spice' => 10,
            'package' => [250 => 40],
        ])->assertOk();

        $this->actingAs($user)->get(route('admin.productions.index'))
            ->assertOk()
            ->assertSee('Daily Summary')
            ->assertSee('10kg × 7');

        $this->actingAs($user)->get(route('admin.spice-productions.index'))
            ->assertOk()
            ->assertSee('Daily Summary')
            ->assertSee('250g × 40');
    }
}
