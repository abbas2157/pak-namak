<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Packaged output of a salt production batch — how many thaila (per kg
     * size) and how many package bundles (per gram × 10/20-pack) were made.
     * Mirrors the thaila/package lines of `stocks`.
     */
    public function up(): void
    {
        Schema::create('production_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained()->cascadeOnDelete();
            $table->string('product_type'); // thaila | package
            $table->unsignedInteger('size'); // thaila: kg, package: grams
            $table->unsignedInteger('bundle_size')->nullable(); // package only: 10 / 20
            $table->decimal('quantity', 10, 2); // thaila count / bundle count
            $table->decimal('quantity_kg', 10, 2);
            $table->timestamps();

            $table->index(['production_id', 'product_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_items');
    }
};
