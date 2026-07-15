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
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->text('note')->nullable()->after('payment_method');
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
        });

        // Backfill: every sale that already has a received_amount but no payment
        // rows behind it gets one 'legacy' payment so switching received_amount
        // over to a payments-derived value doesn't zero out existing history.
        $sales = DB::table('sales')->where('received_amount', '>', 0)->get();

        foreach ($sales as $sale) {
            $hasPayments = DB::table('sale_payments')->where('sale_id', $sale->id)->exists();

            if (!$hasPayments) {
                DB::table('sale_payments')->insert([
                    'sale_id'        => $sale->id,
                    'amount'         => $sale->received_amount,
                    'payment_date'   => $sale->sale_date,
                    'payment_method' => 'legacy',
                    'note'           => 'Backfilled from existing received amount',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropColumn('note');
        });
    }
};
