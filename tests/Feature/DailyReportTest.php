<?php

namespace Tests\Feature;

use App\Models\SpiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReportTest extends TestCase
{
    use RefreshDatabase;

    private function seedProduction(User $user): void
    {
        $spice = SpiceType::first() ?? SpiceType::create(['title' => 'Chilli Powder']);
        $today = now()->toDateString();

        $this->actingAs($user)->postJson(route('admin.productions.store'), [
            'production_date' => $today,
            'raw_salt_used' => 100,
            'finished_salt' => 90,
            'thaila' => [10 => 7],
            'package' => [500 => [10 => 3]], // 30 packets
        ])->assertOk();

        $this->actingAs($user)->postJson(route('admin.spice-productions.store'), [
            'spice_type_id' => $spice->id,
            'production_date' => $today,
            'raw_spice_used' => 10,
            'finished_spice' => 10,
            'package' => [250 => 40],
        ])->assertOk();
    }

    public function test_daily_production_report_totals_and_high_low(): void
    {
        $user = User::factory()->create();
        $this->seedProduction($user);

        $res = $this->actingAs($user)->get(route('admin.reports.daily_production'))->assertOk();
        $res->assertSee('Daily Production Report');
        $res->assertViewHas('totals', fn ($t) => (float) $t['sp_thaila'] === 7.0
            && (float) $t['sp_packets'] === 30.0
            && (float) $t['sp_finished'] === 90.0
            && (float) $t['spp_packets'] === 40.0
            && (float) $t['total_finished'] === 100.0
            && (float) $t['total_packets'] === 70.0);
        $res->assertViewHas('activeDays', 1);
        $res->assertViewHas('highLow', fn ($h) => $h['total']['high']['value'] === 100.0 && $h['total']['high']['share'] === 100.0);
        // No previous-month data → no percentage to show
        $res->assertViewHas('changes', fn ($c) => $c['total_finished'] === null);

        $this->actingAs($user)->get(route('admin.reports.daily_production', ['month' => now()->subMonth()->format('Y-m')]))
            ->assertOk()
            ->assertViewHas('activeDays', 0);
    }

    public function test_daily_sales_report_renders_and_ignores_production(): void
    {
        $user = User::factory()->create();
        $this->seedProduction($user);

        $res = $this->actingAs($user)->get(route('admin.reports.daily_sales'))->assertOk();
        $res->assertSee('Daily Sales Report');
        $res->assertViewHas('activeDays', 0);
        $res->assertViewHas('highLow', fn ($h) => $h['total'] === null);
        $res->assertViewHas('charts', fn ($c) => count($c['days']) === (int) now()->day && array_sum($c['salt_amount']) === 0.0);
    }
}
