<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Owner's personal money moving to/from Pak Namak. Deliberately
     * standalone — no account_id FK and no Cash Ledger sync, so it never
     * touches sales, profit, investments or account balances.
     */
    public function up(): void
    {
        Schema::create('owner_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['taken', 'put_in']);
            $table->decimal('amount', 12, 2);
            $table->date('transaction_date');
            $table->string('purpose');
            $table->string('paid_via')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_transactions');
    }
};
