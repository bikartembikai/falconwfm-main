<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users (Update existing or create if fresh)
        // Assuming 'users' exists, we ensuring fields.
        Schema::table('users', function (Blueprint $table) {
             if (!Schema::hasColumn('users', 'role')) {
                 $table->string('role')->default('user'); // user, facilitator, admin
             }
        });

        // 2. Facilitators (Recreating to match diagram + new requirements)
        Schema::dropIfExists('facilitators');
        Schema::dropIfExists('facilitator_profile'); // Cleanup old table if exists

        Schema::create('facilitators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('skills')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('experience')->nullable(); // Request: Experience
            $table->date('join_date')->nullable();   // Request: Join Date
            $table->timestamps();
        });

        // 3. Events (Recreating/Updating)
        Schema::dropIfExists('events');
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->string('venue')->nullable();
            $table->text('required_skill_tag')->nullable();
            $table->string('status')->default('upcoming'); // upcoming, ongoing, completed
            $table->integer('quota')->default(0);
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time')->nullable();
            $table->timestamps();
        });

        // 4. Assignments (New - Many-to-Many User/Facilitator <-> Event)
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            // Determining who is assigned. Generally a User or Facilitator.
            // Diagram links User checks 'assigns'. Let's link to User for flexibility, or Facilitator.
            // Given "Facilitator" inherits "User", linking to User ID is safer for all roles.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('date_assigned')->useCurrent();
            $table->string('role')->nullable(); // e.g. 'Lead Facilitator', 'Support'
            $table->timestamps();
        });

        // 5. Attendance (New)
        Schema::dropIfExists('attendance_records'); // Cleanup old
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('facilitator_id')->constrained('facilitators')->onDelete('cascade');
            $table->dateTime('clock_in_time')->nullable();
            $table->dateTime('clock_out_time')->nullable();
            $table->string('status')->default('absent'); // present, absent, late
            $table->string('image_proof')->nullable();
            $table->timestamps();
        });

        // 6. Payments (New)
        Schema::dropIfExists('payment_records'); // Cleanup old
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_status')->default('pending'); // pending, paid
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });

        // 7. Performance Reviews (New)
        Schema::dropIfExists('performance_reviews'); // Cleanup old
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facilitator_id')->constrained('facilitators')->onDelete('cascade');
            $table->integer('rating')->comment('1-5 stars');
            $table->text('feedback_comments')->nullable();
            $table->date('date_submitted')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('events');
        Schema::dropIfExists('facilitators');
        
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
