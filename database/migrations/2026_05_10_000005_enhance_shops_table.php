<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // email was non-nullable unique — make nullable so shops without email work
            $table->string('email')->nullable()->change();

            $table->string('owner_name')->nullable()->after('name');
            $table->string('city')->nullable()->after('address');
            $table->string('status')->default('active')->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'city', 'status']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
