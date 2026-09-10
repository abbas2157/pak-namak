<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * sale_dallas / sale_thailas / sale_packages were created with a bare
 * `foreignId('sale_id')` and no constraint, so deleting a sale left its line
 * items behind forever. Those orphans were still being counted by the sales
 * report's product totals.
 */
return new class extends Migration
{
    private array $tables = ['sale_dallas', 'sale_thailas', 'sale_packages'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            // Existing orphans would block the constraint from being created.
            DB::table($table)
                ->whereNotIn('sale_id', fn ($q) => $q->select('id')->from('sales'))
                ->delete();

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreign('sale_id')
                    ->references('id')
                    ->on('sales')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropForeign($table.'_sale_id_foreign');
            });
        }
    }
};
