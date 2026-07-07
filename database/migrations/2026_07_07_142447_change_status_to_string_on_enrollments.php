<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Converts the strict ENUM column into a flexible String column 
        // while safely keeping all your existing student statuses!
        DB::statement("ALTER TABLE enrollments MODIFY status VARCHAR(50) DEFAULT 'requested'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverts back to the original strict ENUM if needed
        DB::statement("ALTER TABLE enrollments MODIFY status ENUM('requested', 'pending_payment', 'postpay', 'paid', 'free') DEFAULT 'requested'");
    }
};