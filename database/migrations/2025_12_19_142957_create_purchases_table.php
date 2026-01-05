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
        Schema::create('salt_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->decimal('salt_quantity', 10, 2);
            $table->decimal('rate_per_kg', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->decimal('transport_cost', 10, 2)->default(0);
            $table->decimal('loading_unloading_cost', 10, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
