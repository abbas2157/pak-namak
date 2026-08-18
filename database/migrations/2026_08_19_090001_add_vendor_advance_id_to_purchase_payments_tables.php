<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Marks a payment as drawn from an existing vendor advance rather
        // than a fresh account transaction — lets the payment's own
        // CashLedger sync be skipped, since that cash already left the
        // business when the advance itself was recorded.
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->foreignId('vendor_advance_id')->nullable()->after('account_id')->constrained('vendor_advances')->nullOnDelete();
        });

        Schema::table('spice_purchase_payments', function (Blueprint $table) {
            $table->foreignId('vendor_advance_id')->nullable()->after('account_id')->constrained('vendor_advances')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_advance_id');
        });

        Schema::table('spice_purchase_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_advance_id');
        });
    }
};
