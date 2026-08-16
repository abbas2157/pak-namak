<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spice_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('size'); // grams
            $table->decimal('quantity', 10, 2)->default(0); // packets
            $table->decimal('quantity_kg', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['spice_type_id', 'size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spice_stocks');
    }
};
