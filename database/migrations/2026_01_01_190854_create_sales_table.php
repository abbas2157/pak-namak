<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('salt_type_id');
            $table->string('product_size');
            $table->integer('quantity_sold');
            $table->float('rate_per_pack', 8, 2);
            $table->float('total_sales_amount', 10, 2);
            $table->text('remarks')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('salt_type_id')->references('id')->on('salt_type')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
