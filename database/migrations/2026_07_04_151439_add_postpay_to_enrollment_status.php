<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rebuilds the ENUM list to include 'postpay' alongside your existing options
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('requested', 'pending_payment', 'postpay', 'paid', 'free') NOT NULL DEFAULT 'requested'");
    }

    public function down(): void
    {
        // Rolls back to the original list if needed
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('requested', 'pending_payment', 'paid', 'free') NOT NULL DEFAULT 'requested'");
    }
};
