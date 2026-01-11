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
        Schema::create('sale_dallas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id');

            $table->decimal('quantity_mann', 8, 2);
            $table->decimal('quantity_kg', 8, 2);

            $table->decimal('price_per_mann', 10, 2);
            $table->decimal('price_per_kg', 10, 2);

            $table->decimal('sub_total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_dallas');
    }
};
