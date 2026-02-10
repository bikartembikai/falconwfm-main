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
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->foreignId('reviewerID')->nullable()->constrained('users', 'userID')->onDelete('cascade');
            $table->foreignId('assignmentID')->nullable()->constrained('assignments', 'assignmentID')->onDelete('cascade');
            $table->foreignId('facilitatorID')->nullable()->constrained('users', 'userID')->onDelete('cascade');
            $table->dateTime('reviewDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->dropForeign(['reviewerID']);
            $table->dropColumn('reviewerID');
            $table->dropForeign(['assignmentID']);
            $table->dropColumn('assignmentID');
            $table->dropForeign(['facilitatorID']);
            $table->dropColumn('facilitatorID');
            $table->dropColumn('reviewDate');
        });
    }
};
