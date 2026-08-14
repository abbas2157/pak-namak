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
        Schema::create('cash_ledger', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // in | out
            $table->decimal('amount', 12, 2);
            $table->string('source_type'); // sale_payment | expense | purchase_payment | employee_salary | manual | opening_balance
            $table->unsignedBigInteger('source_id')->nullable();
            $table->date('transaction_date');
            $table->string('description');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_ledger');
    }
};
