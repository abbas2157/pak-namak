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
        Schema::create('sale_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id');

            $table->integer('packet_gram');
            $table->integer('bundle_size');
            $table->integer('bundle_quantity');

            $table->decimal('total_kg', 8, 3);

            $table->decimal('price_per_bundle', 10, 2);
            $table->decimal('sub_total', 12, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_packages');
    }
};
