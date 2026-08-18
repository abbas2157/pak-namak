<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Spice sales are now priced per KG rather than per packet.
        Schema::table('spice_sale_items', function (Blueprint $table) {
            $table->renameColumn('price_per_unit', 'price_per_kg');
        });
    }

    public function down(): void
    {
        Schema::table('spice_sale_items', function (Blueprint $table) {
            $table->renameColumn('price_per_kg', 'price_per_unit');
        });
    }
};
