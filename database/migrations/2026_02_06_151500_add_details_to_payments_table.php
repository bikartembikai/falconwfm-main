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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('title')->nullable()->after('assignmentID'); // e.g. "Travel Allowance"
            $table->text('description')->nullable()->after('amount');
            // Ensure paymentDate defaults to now if not set, or we handle it in controller
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }
};
