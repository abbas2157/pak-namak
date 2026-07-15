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
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropUnique(['product_type', 'size']);
            $table->unsignedInteger('bundle_size')->nullable()->after('size');
            $table->unique(['product_type', 'size', 'bundle_size']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedInteger('bundle_size')->nullable()->after('size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropUnique(['product_type', 'size', 'bundle_size']);
            $table->dropColumn('bundle_size');
            $table->unique(['product_type', 'size']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('bundle_size');
        });
    }
};
