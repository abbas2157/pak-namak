<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('id')->constrained('accounts')->nullOnDelete();
            $table->boolean('is_investment')->default(false)->after('purchase_date');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('is_investment')->default(false)->after('expense_date');
        });

        Schema::table('salt_purchases', function (Blueprint $table) {
            $table->boolean('is_investment')->default(false)->after('purchase_date');
        });

        Schema::table('cash_ledger', function (Blueprint $table) {
            $table->boolean('is_investment')->default(false)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_id');
            $table->dropColumn('is_investment');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('is_investment');
        });

        Schema::table('salt_purchases', function (Blueprint $table) {
            $table->dropColumn('is_investment');
        });

        Schema::table('cash_ledger', function (Blueprint $table) {
            $table->dropColumn('is_investment');
        });
    }
};
