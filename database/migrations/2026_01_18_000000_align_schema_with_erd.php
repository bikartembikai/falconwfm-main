<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Disable FK checks to allow dropping tables freely
        Schema::disableForeignKeyConstraints();
        
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('leaves'); // New Table
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('events');
        Schema::dropIfExists('facilitators');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions'); // Standard Laravel table, usually keep but let's be clean
        Schema::dropIfExists('password_reset_tokens');
        
        Schema::enableForeignKeyConstraints();

        // 1. Users (ERD: User)
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // userID
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // password
            $table->string('role')->default('user'); // role (admin, facilitator, user)
            $table->rememberToken();
            $table->timestamps();
        });

        // Standard Laravel Authentication Support Tables
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 2. Facilitators (ERD: Facilitator - inherits User via 1:1)
        Schema::create('facilitators', function (Blueprint $table) {
            $table->id(); 
            // Relationship
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Attributes from ERD
            $table->text('skills')->nullable();
            $table->string('bank_name')->nullable(); // bankName
            $table->string('bank_account_number')->nullable(); // bankAccountNumber
            $table->string('phone_number')->nullable(); // phoneNumber
            $table->text('experience')->nullable(); // experience
            $table->date('join_date')->nullable(); // joinDate
            $table->float('average_rating')->default(0); // averageRating
            // Additional typical fields
            $table->text('certifications')->nullable();
            $table->timestamps();
        });

        // 3. Events (ERD: Event)
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // eventID
            $table->string('event_name'); // eventName
            $table->string('venue')->nullable(); // venue
            $table->text('event_description')->nullable(); // eventDescription
            $table->string('event_category')->nullable(); // eventCategory
            $table->string('status')->default('upcoming'); // status
            $table->integer('quota')->default(0); // quota
            $table->dateTime('start_date_time'); // startDateTime
            $table->dateTime('end_date_time')->nullable(); // endDateTime
            $table->text('required_skill_tag')->nullable(); // Helper col
            $table->timestamps();
        });

        // 4. Assignments (ERD: Assignment)
        Schema::create('assignments', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Facilitator is a User
            $table->dateTime('date_assigned')->useCurrent(); // dateAssigned
            $table->string('role')->nullable(); // role (Lead, Support)
            $table->timestamps();
        });

        // 5. Attendance (ERD: Attendance)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id(); // attendanceID
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            // Link to Facilitator (table) or User? ERD has 'does' relation to Facilitator.
            // Let's link to Facilitator ID for strictness if ERD implies facilitator entity.
            // Using user_id is often flexible, but let's stick to Facilitator ID if possible or User ID. 
            // Codebase uses User ID mostly for auth. But let's check Seeder. 
            // Let's link to facilitators table.
            $table->foreignId('facilitator_id')->constrained('facilitators')->onDelete('cascade');
            
            $table->dateTime('clock_in_time')->nullable(); // clockInTime
            $table->dateTime('clock_out_time')->nullable(); // clockOutTime
            $table->string('status')->default('absent'); // status (present, late, absent)
            $table->string('image_proof')->nullable(); // imageProof
            $table->timestamps();
        });

        // 6. Payment (ERD: Payment)
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // paymentID
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->decimal('amount', 10, 2); // amount
            $table->string('payment_status')->default('pending'); // paymentStatus
            $table->string('payment_proof')->nullable(); // paymentProof (Requested Update)
            $table->date('payment_date')->nullable(); // paymentDate
            $table->timestamps();
        });

        // 7. PerformanceReview (ERD: PerformanceReview)
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id(); // reviewID
            $table->foreignId('facilitator_id')->constrained('facilitators')->onDelete('cascade'); // 'writes' relation... normally Admin writes on Facilitator? Or Facilitator writes? 
            // Usually Review is ABOUT a facilitator. 
            // Let's add reviewer_id (User) if needed, but ERD just has FK to Facilitator (likely the target).
            $table->integer('rating'); // rating
            $table->text('feedback_comments')->nullable(); // feedbackComments
            $table->date('date_submitted')->useCurrent(); // dateSubmitted
            $table->string('role')->nullable(); // role (context of review)
            $table->timestamps();
        });

        // 8. Leave (ERD: Leave - NEW)
        Schema::create('leaves', function (Blueprint $table) {
            $table->id(); // leaveID
            // 'applies' relation to Facilitator (or User). using User ID allows normal users too, but ERD connects to Facilitator.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->date('start_date'); // startDate
            $table->date('end_date'); // endDate
            $table->string('status')->default('pending'); // status
            $table->text('reason')->nullable(); // reason
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('events');
        Schema::dropIfExists('facilitators');
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
};
