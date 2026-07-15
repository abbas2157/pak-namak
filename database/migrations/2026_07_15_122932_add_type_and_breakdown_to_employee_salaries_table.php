<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            // The composite unique index also backs the employee_id FK constraint —
            // give the FK a plain index to fall back on before dropping the unique one.
            $table->index('employee_id');
            $table->dropUnique(['employee_id', 'month']);
            $table->string('type')->default('salary')->after('employee_id');
            $table->decimal('gross_amount', 10, 2)->nullable()->after('amount');
            $table->decimal('advance_deducted', 10, 2)->nullable()->default(0)->after('gross_amount');
            $table->unsignedInteger('absent_days')->nullable()->default(0)->after('advance_deducted');
            $table->decimal('absence_deducted', 10, 2)->nullable()->default(0)->after('absent_days');
        });

        // Backfill: every existing row was the one flat "salary" payment for that
        // month — no historical advance/absence breakdown existed before this.
        DB::table('employee_salaries')->update([
            'type'         => 'salary',
            'gross_amount' => DB::raw('amount'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->dropColumn(['type', 'gross_amount', 'advance_deducted', 'absent_days', 'absence_deducted']);
            $table->unique(['employee_id', 'month']);
            $table->dropIndex(['employee_id']);
        });
    }
};
