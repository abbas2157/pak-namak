<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // cash | bank | jazzcash | easypaisa | other
            $table->string('bank_name')->nullable(); // only for type=bank, picked from a fixed bank list
            $table->string('account_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // A business always has at least Cash + the two mobile wallets out of
        // the box, so payment forms have something to select immediately.
        DB::table('accounts')->insert([
            ['name' => 'Cash in Hand', 'type' => 'cash',      'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'JazzCash',     'type' => 'jazzcash',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'EasyPaisa',    'type' => 'easypaisa', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
