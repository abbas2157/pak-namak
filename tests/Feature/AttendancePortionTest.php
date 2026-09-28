<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeAbsence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendancePortionTest extends TestCase
{
    use RefreshDatabase;

    private function employee(float $salary = 30000): Employee
    {
        return Employee::create([
            'name' => 'Aslam',
            'email' => 'aslam'.uniqid().'@paknamak.test',
            'phone' => '0300',
            'salary' => $salary,
            // status left at its default: on SQLite the column still carries the
            // pre-migration enum check (the ALTER is MySQL-only), and attendance
            // does not depend on it.
        ]);
    }

    private function mark(Employee $employee, array $data)
    {
        return $this->actingAs(User::factory()->create())
            ->postJson(route('admin.employees.absences.store', $employee), $data);
    }

    public function test_absence_defaults_to_a_full_day(): void
    {
        $employee = $this->employee();

        $this->mark($employee, ['date' => '2026-09-01'])->assertOk();

        $absence = EmployeeAbsence::first();
        $this->assertSame(1.0, (float) $absence->portion);
        $this->assertSame('Full Day', $absence->portionLabel());
    }

    public function test_half_and_quarter_days_are_stored_and_labelled(): void
    {
        $employee = $this->employee();

        $this->mark($employee, ['date' => '2026-09-02', 'portion' => '0.50'])->assertOk();
        $this->mark($employee, ['date' => '2026-09-03', 'portion' => '0.25'])->assertOk();

        $half = EmployeeAbsence::whereDate('date', '2026-09-02')->first();
        $quarter = EmployeeAbsence::whereDate('date', '2026-09-03')->first();

        $this->assertSame(0.5, (float) $half->portion);
        $this->assertSame('Half Day', $half->portionLabel());
        $this->assertSame(0.25, (float) $quarter->portion);
        $this->assertSame('Quarter Day', $quarter->portionLabel());
    }

    public function test_an_invalid_portion_is_rejected(): void
    {
        $this->mark($this->employee(), ['date' => '2026-09-04', 'portion' => '0.75'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('portion');
    }

    public function test_deduction_uses_fractional_days(): void
    {
        $employee = $this->employee(30000); // 1000 per day

        $this->mark($employee, ['date' => '2026-09-01'])->assertOk();                      // 1
        $this->mark($employee, ['date' => '2026-09-02', 'portion' => '0.50'])->assertOk(); // 0.5
        $this->mark($employee, ['date' => '2026-09-03', 'portion' => '0.25'])->assertOk(); // 0.25
        // Paid leave is never deducted, whatever its size
        $this->mark($employee, ['date' => '2026-09-04', 'portion' => '0.50', 'paid' => 1])->assertOk();

        $this->actingAs(User::factory()->create())
            ->getJson(route('admin.employees.salaries.preview', $employee).'?month=2026-09-01')
            ->assertOk()
            ->assertJsonPath('absent_days', fn ($v) => (float) $v === 1.75)
            ->assertJsonPath('absence_deducted', fn ($v) => (float) $v === 1750.0)
            ->assertJsonPath('net', fn ($v) => (float) $v === 28250.0);
    }

    public function test_settled_month_records_the_fractional_day_count(): void
    {
        $employee = $this->employee(30000);
        $user = User::factory()->create();

        $this->mark($employee, ['date' => '2026-09-01', 'portion' => '0.50'])->assertOk();

        $this->actingAs($user)
            ->postJson(route('admin.employees.salaries.store', $employee), ['month' => '2026-09-01'])
            ->assertOk();

        $salary = $employee->salaries()->where('type', 'salary')->first();
        $this->assertSame(0.5, (float) $salary->absent_days);
        $this->assertSame(500.0, (float) $salary->absence_deducted);
        $this->assertSame(29500.0, (float) $salary->amount);

        $this->actingAs($user)
            ->get(route('admin.employees.salaries.payslip', [$employee, $salary]))
            ->assertOk()
            ->assertSee('0.5 day');
    }
}
