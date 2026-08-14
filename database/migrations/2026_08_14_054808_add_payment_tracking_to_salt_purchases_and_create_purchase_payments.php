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
        Schema::table('salt_purchases', function (Blueprint $table) {
            $table->decimal('paid_amount', 12, 2)->default(0)->after('grand_total');
            $table->decimal('pending_amount', 12, 2)->default(0)->after('paid_amount');
        });

        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('salt_purchases')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Backfill: there was never a payment concept before this, so assume every
        // existing purchase was already settled the old way — one legacy payment
        // per purchase equal to its full grand_total, rather than suddenly showing
        // fabricated payables for purchases that were, in practice, already paid.
        $purchases = DB::table('salt_purchases')->get();

        foreach ($purchases as $purchase) {
            DB::table('purchase_payments')->insert([
                'purchase_id'  => $purchase->id,
                'amount'       => $purchase->grand_total,
                'payment_date' => $purchase->purchase_date,
                'note'         => 'Backfilled — assumed fully paid before payment tracking existed',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        DB::table('salt_purchases')->update([
            'paid_amount'    => DB::raw('grand_total'),
            'pending_amount' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');

        Schema::table('salt_purchases', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'pending_amount']);
        });
    }
};
