<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Added during registration
            $table->string('al_batch')->nullable();

            // Added later in the profile page
            $table->text('address')->nullable();
            $table->date('dob')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['al_batch', 'address', 'dob']);
        });
    }
};
