<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('cnic', 20)->nullable()->unique()->after('email');
            $table->string('designation', 100)->nullable()->after('cnic');
            $table->date('joining_date')->nullable()->after('address');
            $table->date('leave_date')->nullable()->after('joining_date');
        });

        // Map old enum values to new ones before changing the column
        DB::statement("UPDATE employees SET status = 'working' WHERE status = 'active'");
        DB::statement("UPDATE employees SET status = 'terminated' WHERE status = 'inactive'");
        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('working','on_leave','terminated') NOT NULL DEFAULT 'working'");
    }

    public function down(): void
    {
        DB::statement("UPDATE employees SET status = 'active' WHERE status IN ('working','on_leave')");
        DB::statement("UPDATE employees SET status = 'inactive' WHERE status = 'terminated'");
        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'active'");

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['cnic', 'designation', 'joining_date', 'leave_date']);
        });
    }
};
