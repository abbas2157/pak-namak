<?php

namespace Tests\Feature;

use App\Models\SpiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionTouchedPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_pages_render_with_production_movements(): void
    {
        $user = User::factory()->create();
        $spice = SpiceType::first() ?? SpiceType::create(['title' => 'Chilli Powder']);

        $this->actingAs($user)->postJson(route('admin.productions.store'), [
            'production_date' => now()->toDateString(),
            'raw_salt_used' => 100,
            'finished_salt' => 100,
            'electricity_fuel_cost' => 250,
            'thaila' => [10 => 7],
        ])->assertOk();

        $this->actingAs($user)->postJson(route('admin.spice-productions.store'), [
            'spice_type_id' => $spice->id,
            'production_date' => now()->toDateString(),
            'raw_spice_used' => 10,
            'finished_spice' => 10,
            'package' => [250 => 40],
        ])->assertOk();

        $this->actingAs($user)->get(route('admin.stocks.index'))->assertOk()->assertSee('production');
        $this->actingAs($user)->get(route('admin.spice-stock.index'))->assertOk()->assertSee('production');
    }
}
