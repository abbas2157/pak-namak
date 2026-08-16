<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spice_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spice_type_id')->constrained()->cascadeOnDelete();
            $table->date('purchase_date')->nullable();
            $table->decimal('quantity_kg', 10, 2);
            $table->decimal('rate_per_kg', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->decimal('transport_cost', 12, 2)->default(0);
            $table->decimal('loading_unloading_cost', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('pending_amount', 12, 2)->default(0);
            $table->boolean('is_investment')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('spice_purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spice_purchase_payments');
        Schema::dropIfExists('spice_purchases');
    }
};
