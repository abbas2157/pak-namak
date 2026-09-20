<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Shop;
use App\Models\SpiceSale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShopAreaTest extends TestCase
{
    use RefreshDatabase;

    private function shopWithArea(): Shop
    {
        $city = City::create(['name' => 'Lahore']);
        $area = Area::create(['city_id' => $city->id, 'name' => 'Anarkali']);

        return Shop::create([
            'name' => 'Test Mart', 'phone_number' => '03001234567', 'address' => 'Main Rd', 'city' => 'Lahore',
            'city_id' => $city->id, 'area_id' => $area->id, 'status' => 'active',
        ]);
    }

    public function test_shop_location_puts_area_before_city(): void
    {
        $shop = $this->shopWithArea();
        $this->assertSame('Anarkali, Lahore', $shop->fresh()->location);

        $shop->update(['area_id' => null]);
        $this->assertSame('Lahore', $shop->fresh()->location);
    }

    public function test_both_public_order_forms_show_area_in_shop_dropdown(): void
    {
        $this->shopWithArea();

        $this->get(route('order.form'))->assertOk()->assertSee('Test Mart — Anarkali, Lahore');
        $this->get(route('spice-order.form'))->assertOk()->assertSee('Test Mart — Anarkali, Lahore');
    }

    public function test_spice_order_shop_info_returns_spice_pending_only(): void
    {
        $shop = $this->shopWithArea();
        SpiceSale::create(['shop_id' => $shop->id, 'sale_date' => now(), 'total_amount' => 5000, 'received_amount' => 2000, 'pending_amount' => 3000]);

        $this->getJson(route('spice-order.shop.info', $shop))
            ->assertOk()
            ->assertJsonPath('shop.location', 'Anarkali, Lahore')
            ->assertJsonPath('financials.pending_amount', 3000)
            ->assertJsonPath('financials.total_amount', 5000);

        // Salt endpoint is unaffected by spice sales
        $this->getJson(route('order.shop.info', $shop))
            ->assertOk()
            ->assertJsonPath('financials.pending_amount', 0);
    }

    public function test_admin_shop_info_exposes_location_and_spice_orders(): void
    {
        $shop = $this->shopWithArea();
        SpiceSale::create(['shop_id' => $shop->id, 'sale_date' => now(), 'total_amount' => 5000, 'received_amount' => 2000, 'pending_amount' => 3000]);

        $this->actingAs(User::factory()->create())
            ->getJson(route('admin.shops.info', $shop))
            ->assertOk()
            ->assertJsonPath('shop.location', 'Anarkali, Lahore')
            ->assertJsonPath('financials.spice_pending', 3000)
            ->assertJsonPath('financials.pending_amount', 3000)
            ->assertJsonStructure(['spice_orders']);
    }

    public function test_spice_sale_form_has_account_summary_panel(): void
    {
        $this->shopWithArea();

        $this->actingAs(User::factory()->create())
            ->get(route('admin.spice-sales.create'))
            ->assertOk()
            ->assertSee('shop-info-panel')
            ->assertSee('Account Summary')
            ->assertSee('Test Mart — Anarkali, Lahore');
    }
}
