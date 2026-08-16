<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spice_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spice_order_id')->nullable()->constrained('spice_orders')->nullOnDelete();
            $table->date('sale_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('received_amount', 12, 2)->default(0);
            $table->decimal('pending_amount', 12, 2)->default(0);
            $table->string('bill_image')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('spice_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spice_type_id')->constrained()->cascadeOnDelete();
            $table->integer('packet_gram');
            $table->integer('quantity'); // packets
            $table->decimal('total_kg', 8, 3);
            $table->decimal('price_per_unit', 10, 2);
            $table->decimal('sub_total', 12, 2);
            $table->timestamps();
        });

        Schema::create('spice_sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spice_sale_payments');
        Schema::dropIfExists('spice_sale_items');
        Schema::dropIfExists('spice_sales');
    }
};
