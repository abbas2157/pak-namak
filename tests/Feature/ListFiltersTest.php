<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Sale;
use App\Models\Shop;
use App\Models\SpiceSale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListFiltersTest extends TestCase
{
    use RefreshDatabase;

    private function fixtures(): array
    {
        $city = City::create(['name' => 'Lahore']);
        $a1 = Area::create(['city_id' => $city->id, 'name' => 'Anarkali']);
        $a2 = Area::create(['city_id' => $city->id, 'name' => 'Model Town']);

        $mk = fn ($name, $area) => Shop::create([
            'name' => $name, 'phone_number' => '0300', 'address' => 'x', 'city' => 'Lahore',
            'city_id' => $city->id, 'area_id' => $area->id, 'status' => 'active',
        ]);
        $s1 = $mk('Alpha Mart', $a1);
        $s2 = $mk('Beta Store', $a2);
        $s3 = $mk('Gamma Shop', $a2);

        Sale::create(['shop_id' => $s1->id, 'sale_date' => '2026-09-05', 'total_amount' => 1000, 'received_amount' => 1000, 'pending_amount' => 0]);
        Sale::create(['shop_id' => $s2->id, 'sale_date' => '2026-09-15', 'total_amount' => 2000, 'received_amount' => 500, 'pending_amount' => 1500]);
        SpiceSale::create(['shop_id' => $s2->id, 'sale_date' => '2026-08-20', 'total_amount' => 300, 'received_amount' => 300, 'pending_amount' => 0]);

        return compact('a1', 'a2', 's1', 's2', 's3');
    }

    public function test_sales_index_filters_by_area_shop_and_date(): void
    {
        $d = $this->fixtures();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.sales.index', ['area_id' => $d['a2']->id]))
            ->assertOk()->assertViewHas('totalCount', 1)->assertViewHas('totalRevenue', 2000.0);

        $this->actingAs($user)->get(route('admin.sales.index', ['city_id' => $d['s1']->city_id]))
            ->assertOk()->assertViewHas('totalCount', 2)->assertSee('All Cities');
        $this->actingAs($user)->get(route('admin.sales.index', ['city_id' => 999]))
            ->assertOk()->assertViewHas('totalCount', 0);

        $this->actingAs($user)->get(route('admin.sales.index', ['shop_id' => $d['s1']->id]))
            ->assertOk()->assertViewHas('totalCount', 1)->assertViewHas('totalRevenue', 1000.0);

        $this->actingAs($user)->get(route('admin.sales.index', ['from' => '2026-09-10', 'to' => '2026-09-30']))
            ->assertOk()->assertViewHas('totalCount', 1)->assertViewHas('totalRevenue', 2000.0);

        $this->actingAs($user)->get(route('admin.sales.index', ['from' => '2026-09-01', 'area_id' => $d['a1']->id, 'shop_id' => $d['s2']->id]))
            ->assertOk()->assertViewHas('totalCount', 0);

        $this->actingAs($user)->get(route('admin.sales.index'))->assertOk()->assertViewHas('totalCount', 2)->assertDontSee('Clear Filters');
    }

    public function test_spice_sales_index_filters_by_area_and_date(): void
    {
        $d = $this->fixtures();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.spice-sales.index', ['area_id' => $d['a2']->id, 'from' => '2026-08-01', 'to' => '2026-08-31']))
            ->assertOk()->assertViewHas('totalCount', 1);
        $this->actingAs($user)->get(route('admin.spice-sales.index', ['area_id' => $d['a1']->id]))
            ->assertOk()->assertViewHas('totalCount', 0);
    }

    public function test_shops_index_filters_by_area_and_sales(): void
    {
        $d = $this->fixtures();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.shops.index', ['city_id' => $d['s1']->city_id]))
            ->assertOk()->assertViewHas('totalShops', 3);
        $this->actingAs($user)->get(route('admin.shops.index', ['area_id' => $d['a2']->id]))
            ->assertOk()->assertViewHas('totalShops', 2);
        $this->actingAs($user)->get(route('admin.shops.index', ['sales' => 'with']))
            ->assertOk()->assertViewHas('totalShops', 2);
        $this->actingAs($user)->get(route('admin.shops.index', ['sales' => 'pending']))
            ->assertOk()->assertViewHas('totalShops', 1)->assertSee('Beta Store');
        $this->actingAs($user)->get(route('admin.shops.index', ['sales' => 'none']))
            ->assertOk()->assertViewHas('totalShops', 1)->assertSee('Gamma Shop');
    }

    public function test_sales_index_paginates_100_per_page_with_totals_over_all_pages(): void
    {
        $d = $this->fixtures();
        $user = User::factory()->create();

        for ($i = 0; $i < 130; $i++) {
            Sale::create(['shop_id' => $d['s3']->id, 'sale_date' => '2026-07-01', 'total_amount' => 10, 'received_amount' => 4, 'pending_amount' => 6]);
        }

        $res = $this->actingAs($user)->get(route('admin.sales.index'))->assertOk();
        $res->assertViewHas('totalCount', 132);
        $res->assertViewHas('totalRevenue', 1300.0 + 3000.0);
        $res->assertViewHas('sales', fn ($p) => $p->count() === 100 && $p->total() === 132 && $p->lastPage() === 2);
        $res->assertSee('Showing 1–100 of 132 sales');

        $this->actingAs($user)->get(route('admin.sales.index', ['page' => 2]))
            ->assertOk()
            ->assertViewHas('sales', fn ($p) => $p->count() === 32);
    }
}
