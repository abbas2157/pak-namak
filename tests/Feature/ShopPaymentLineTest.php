<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\Shop;
use App\Models\SpiceSale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopPaymentLineTest extends TestCase
{
    use RefreshDatabase;

    private function shopWithBothPending(): array
    {
        $shop = Shop::create(['name' => 'Alpha', 'phone_number' => '0300', 'address' => 'x', 'city' => 'Lahore', 'status' => 'active']);
        $salt = Sale::create(['shop_id' => $shop->id, 'sale_date' => '2026-09-01', 'total_amount' => 1000, 'received_amount' => 0, 'pending_amount' => 1000]);
        $spice = SpiceSale::create(['shop_id' => $shop->id, 'sale_date' => '2026-08-01', 'total_amount' => 500, 'received_amount' => 0, 'pending_amount' => 500]);

        return compact('shop', 'salt', 'spice');
    }

    private function pay(Shop $shop, array $data)
    {
        return $this->actingAs(User::factory()->create())->postJson(route('admin.shops.payments.store', $shop), $data + [
            'payment_date' => '2026-09-20',
        ]);
    }

    public function test_payment_can_be_applied_to_salt_only(): void
    {
        $d = $this->shopWithBothPending();

        $this->pay($d['shop'], ['amount' => 400, 'product_line' => 'salt'])->assertOk()->assertJson(['sales_paid' => 1]);

        $this->assertSame(600.0, (float) $d['salt']->fresh()->pending_amount);
        $this->assertSame(500.0, (float) $d['spice']->fresh()->pending_amount); // untouched
    }

    public function test_payment_can_be_applied_to_spice_only(): void
    {
        $d = $this->shopWithBothPending();

        $this->pay($d['shop'], ['amount' => 500, 'product_line' => 'spice'])->assertOk()->assertJson(['sales_paid' => 1]);

        $this->assertSame(0.0, (float) $d['spice']->fresh()->pending_amount);
        $this->assertSame(1000.0, (float) $d['salt']->fresh()->pending_amount); // untouched
    }

    public function test_both_settles_oldest_first_across_lines(): void
    {
        $d = $this->shopWithBothPending();

        // Spice sale (Aug) is older than the salt sale (Sep), so it is cleared first.
        $this->pay($d['shop'], ['amount' => 700, 'product_line' => 'both'])->assertOk()->assertJson(['sales_paid' => 2]);

        $this->assertSame(0.0, (float) $d['spice']->fresh()->pending_amount);
        $this->assertSame(800.0, (float) $d['salt']->fresh()->pending_amount);
    }

    public function test_amount_above_the_selected_line_pending_is_refused(): void
    {
        $d = $this->shopWithBothPending();

        $this->pay($d['shop'], ['amount' => 600, 'product_line' => 'spice'])
            ->assertStatus(422)
            ->assertJsonFragment(['success' => false]);

        $this->assertSame(500.0, (float) $d['spice']->fresh()->pending_amount);
        $this->assertSame(1000.0, (float) $d['salt']->fresh()->pending_amount);
    }

    public function test_line_with_no_pending_is_refused(): void
    {
        $shop = Shop::create(['name' => 'Beta', 'phone_number' => '0300', 'address' => 'x', 'city' => 'Lahore', 'status' => 'active']);
        Sale::create(['shop_id' => $shop->id, 'sale_date' => '2026-09-01', 'total_amount' => 100, 'received_amount' => 0, 'pending_amount' => 100]);

        $this->pay($shop, ['amount' => 50, 'product_line' => 'spice'])->assertStatus(422);
        $this->pay($shop, ['amount' => 50, 'product_line' => 'salt'])->assertOk();
    }
}
