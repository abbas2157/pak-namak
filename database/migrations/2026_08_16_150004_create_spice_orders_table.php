<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spice_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('spice_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spice_type_id')->constrained()->cascadeOnDelete();
            $table->integer('size'); // grams
            $table->decimal('quantity', 10, 2); // bundle quantity
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spice_order_items');
        Schema::dropIfExists('spice_orders');
    }
};
