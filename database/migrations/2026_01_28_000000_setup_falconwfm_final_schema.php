<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Disable FK checks to allow dropping tables freely during fresh migration
        Schema::disableForeignKeyConstraints();
        
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('events');
        Schema::dropIfExists('facilitators');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        
        Schema::enableForeignKeyConstraints();

        // 1. Users (Base Entity)
        Schema::create('users', function (Blueprint $table) {
            $table->id(); 
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); 
            $table->string('role')->default('user'); // 'admin', 'facilitator', 'marketing_manager'
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Facilitators (Extended Profile for Users)
        Schema::create('facilitators', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Attributes from FYP Report Data Dictionary (Figure 4.5)
            $table->text('skills')->nullable();
            $table->string('bank_name')->nullable(); 
            $table->string('bank_account_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('experience')->nullable();
            $table->date('join_date')->nullable();
            $table->double('average_rating')->default(0); 
            $table->text('certifications')->nullable();
            
            $table->timestamps();
        });

        // 2a. Event Rules (Knowledge Base Concept from ERD)
        Schema::create('event_rules', function (Blueprint $table) {
            $table->string('event_category')->primary(); // PK as String (e.g. 'CAMP')
            $table->text('required_skills')->nullable(); // JSON or Comma separated
            $table->integer('min_experience')->default(0); // Years
            $table->integer('min_rating')->default(0); // 1-5
            $table->string('intensity_level')->default('Normal'); // 'High Risk', 'Normal'
            $table->timestamps();
        });

        // 3. Events (Core Entity)
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // eventID
            $table->string('event_name'); 
            $table->string('venue')->nullable(); 
            $table->text('event_description')->nullable(); 
            
            // Foreign Key link to EventRule (can be nullable if strictly following diagram flow where not all events might have rules, but good to enforce)
            $table->string('event_category')->nullable();
            $table->foreign('event_category')->references('event_category')->on('event_rules')->onDelete('set null');

            $table->string('status')->default('upcoming'); 
            $table->integer('quota')->default(0); 
            $table->dateTime('start_date_time'); 
            $table->dateTime('end_date_time')->nullable(); 
            $table->text('required_skill_tag')->nullable(); // Helper
            $table->text('remark')->nullable();
            $table->timestamps();
        });

        // 4. Assignments (Linking Facilitators to Events)
        Schema::create('assignments', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Points to User (Facilitator)
            $table->dateTime('date_assigned')->useCurrent(); 
            $table->string('role')->nullable(); // e.g., 'Chief', 'Medic', 'Support'
            $table->timestamps();
        });

        // 5. Attendance (Tracking)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('facilitator_id')->constrained('facilitators')->onDelete('cascade'); // Specific to Facilitator Profile
            
            $table->dateTime('clock_in_time')->nullable(); 
            $table->dateTime('clock_out_time')->nullable(); 
            $table->string('status')->default('absent'); // 'present', 'late', 'absent'
            $table->string('image_proof')->nullable(); 
            $table->timestamps();
        });

        // 6. Payments (Financial)
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->decimal('amount', 10, 2); 
            $table->string('payment_status')->default('pending'); 
            $table->string('payment_proof')->nullable(); 
            $table->date('payment_date')->nullable(); 
            $table->timestamps();
        });

        // 7. Performance Reviews (Feedback)
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('facilitator_id')->constrained('facilitators')->onDelete('cascade');
            $table->integer('rating'); // 1-5
            $table->text('feedback_comments')->nullable(); 
            $table->date('date_submitted')->useCurrent(); 
            $table->string('role')->nullable(); // Context
            $table->timestamps();
        });

        // 8. Leaves (Availability Constraint)
        Schema::create('leaves', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->date('start_date'); 
            $table->date('end_date'); 
            $table->string('status')->default('pending'); 
            $table->text('reason')->nullable(); 
            $table->timestamps();
        });
        
        // Standard Laravel Tables
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
        Schema::dropIfExists('event_rules');
        Schema::dropIfExists('facilitators');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::enableForeignKeyConstraints();
    }
};
