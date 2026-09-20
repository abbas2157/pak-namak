<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Spice production is tracked separately from salt (same rule as the rest
     * of the Spices module). One record = one spice type processed on one day;
     * its items are the packets produced per gram size.
     */
    public function up(): void
    {
        Schema::create('spice_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->date('production_date');
            $table->decimal('raw_spice_used', 10, 2)->default(0); // kg
            $table->decimal('finished_spice', 10, 2)->default(0); // kg
            $table->decimal('wastage', 10, 2)->default(0); // kg
            $table->string('machine_used')->nullable();
            $table->decimal('electricity_fuel_cost', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('production_date');
        });

        Schema::create('spice_production_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_production_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('size'); // grams
            $table->decimal('quantity', 10, 2); // packets
            $table->decimal('quantity_kg', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spice_production_items');
        Schema::dropIfExists('spice_productions');
    }
};
