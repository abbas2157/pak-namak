<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spice_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('size'); // grams
            $table->decimal('quantity', 10, 2); // packets
            $table->decimal('quantity_kg', 10, 2);
            $table->string('reason');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spice_stock_movements');
    }
};
