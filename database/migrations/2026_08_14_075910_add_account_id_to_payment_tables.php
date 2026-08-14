<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $tables = ['cash_ledger', 'sale_payments', 'purchase_payments', 'expenses', 'employee_salaries'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('account_id')->nullable()->after('id')->constrained('accounts')->nullOnDelete();
            });
        }

        // Existing money records predate accounts — default them all to
        // "Cash in Hand" (the first seeded account) rather than leaving them
        // unassigned, so historical balances still add up per account.
        $cashAccountId = DB::table('accounts')->where('type', 'cash')->value('id');

        foreach ($this->tables as $table) {
            DB::table($table)->update(['account_id' => $cashAccountId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropForeign(['account_id']);
                $blueprint->dropColumn('account_id');
            });
        }
    }
};
