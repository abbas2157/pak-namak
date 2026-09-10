<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packaging_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->string('kind'); // thaila | packet
            $table->unsignedInteger('size'); // kg for thaila, grams for packet
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('rate_per_unit', 10, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->decimal('transport_cost', 10, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('pending_amount', 12, 2)->default(0);
            $table->date('purchase_date')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_investment')->default(false);
            $table->timestamps();
        });

        Schema::create('packaging_purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packaging_purchase_id')->constrained('packaging_purchases')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packaging_purchase_payments');
        Schema::dropIfExists('packaging_purchases');
    }
};
