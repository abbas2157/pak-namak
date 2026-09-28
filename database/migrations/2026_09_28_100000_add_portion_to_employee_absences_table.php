<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Attendance is no longer all-or-nothing: a day off can be a full day,
     * half day or quarter day. `portion` is the fraction of the day missed
     * (1.00 / 0.50 / 0.25); existing rows are full days.
     *
     * `employee_salaries.absent_days` becomes decimal so a settled month can
     * record e.g. 2.75 days.
     */
    public function up(): void
    {
        Schema::table('employee_absences', function (Blueprint $table) {
            $table->decimal('portion', 3, 2)->default(1)->after('date');
        });

        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->decimal('absent_days', 6, 2)->nullable()->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employee_absences', function (Blueprint $table) {
            $table->dropColumn('portion');
        });

        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->unsignedInteger('absent_days')->nullable()->default(0)->change();
        });
    }
};
