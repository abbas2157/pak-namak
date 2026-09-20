<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The production form treats wastage / machine / cost as optional, but the
     * original table declared them NOT NULL with no default — a blank field
     * (converted to null by the framework) failed the insert.
     */
    public function up(): void
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->decimal('wastage', 10, 2)->default(0)->change();
            $table->string('machine_used')->nullable()->change();
            $table->decimal('electricity_fuel_cost', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->decimal('wastage', 10, 2)->default(null)->change();
            $table->string('machine_used')->nullable(false)->change();
            $table->decimal('electricity_fuel_cost', 10, 2)->default(null)->change();
        });
    }
};
