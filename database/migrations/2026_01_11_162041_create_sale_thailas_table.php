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
        Schema::create('sale_thailas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id');

            $table->integer('bag_size_kg'); 
            $table->integer('quantity'); 
            $table->decimal('total_kg', 8, 2);

            $table->decimal('price_per_bag', 10, 2);
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
        Schema::dropIfExists('sale_thailas');
    }
};
