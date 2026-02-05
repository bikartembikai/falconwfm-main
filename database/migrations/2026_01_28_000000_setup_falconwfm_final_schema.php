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
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('events');
        Schema::dropIfExists('event_rules');
        Schema::dropIfExists('facilitator_skills'); 
        Schema::dropIfExists('skills'); 
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        
        Schema::enableForeignKeyConstraints();

        // 1. Users (Merged Entity)
        Schema::create('users', function (Blueprint $table) {
            $table->id('userID'); // custom PK
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); 
            $table->string('role')->default('user'); // 'admin', 'facilitator', 'marketing_manager'
            
            // Merged Facilitator Attributes (camelCase)
            $table->string('bankName')->nullable(); 
            $table->string('bankAccountNumber')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->text('experience')->nullable(); 
            $table->date('joinDate')->nullable();
            $table->double('averageRating')->default(0); 
            
            $table->rememberToken();
            $table->timestamps();
        });

        // 1a. Skills (Master List)
        Schema::create('skills', function (Blueprint $table) {
            $table->id('skillID'); // custom PK
            $table->string('skillName')->unique();
            $table->timestamps();
        });

        // 1b. Facilitator Skills (Pivot)
        Schema::create('facilitator_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userID')->constrained('users', 'userID')->onDelete('cascade');
            $table->foreignId('skillID')->constrained('skills', 'skillID')->onDelete('cascade');
            $table->timestamps();
        });
        
        // 2a. Event Rules
        Schema::create('event_rules', function (Blueprint $table) {
            $table->string('eventCategory')->primary(); // camelCase PK
            $table->text('requiredSkill')->nullable(); // array json
            $table->integer('minExperience')->default(0); 
            $table->integer('minRating')->default(0); 
            // requiredSpecialization removed from strict ERD request? 
            // Previous code expected it. I'll re-add it as camelCase to match code expectations unless strictly forbidden.
            // The user said "exactly as inside this relational table". The table 'EventRule' has: eventCategory, requiredSkill, minExperience, minRating. 
            // It DOES NOT show requiredSpecialization. I will omit it to be exact to the diagram.
            $table->timestamps();
        });

        // 3. Events (Core Entity)
        Schema::create('events', function (Blueprint $table) {
            $table->id('eventID'); // custom PK
            $table->string('eventName'); 
            $table->string('venue')->nullable(); 
            $table->text('eventDescription')->nullable(); 
            
            // Foreign Key link to EventRule
            $table->string('eventCategory')->nullable();
            $table->foreign('eventCategory')->references('eventCategory')->on('event_rules')->onDelete('set null');

            $table->string('status')->default('upcoming'); 
            $table->integer('quota')->default(0); 
            $table->dateTime('startDateTime'); 
            $table->dateTime('endDateTime')->nullable(); 
            $table->integer('totalParticipants')->default(0); 
            $table->text('remark')->nullable();
            $table->timestamps();
        });

        // 4. Assignments (Linking Facilitators to Events AND Attendance)
        Schema::create('assignments', function (Blueprint $table) {
            $table->id('assignmentID'); // custom PK
            $table->foreignId('eventID')->constrained('events', 'eventID')->onDelete('cascade');
            $table->foreignId('userID')->constrained('users', 'userID')->onDelete('cascade'); 
            $table->dateTime('dateAssigned')->useCurrent(); 
            
            // Attendance Columns Merged Here
            $table->dateTime('clockInTime')->nullable();
            $table->dateTime('clockOutTime')->nullable();
            $table->string('status')->default('assigned'); 
            $table->string('attendanceStatus')->default('absent'); 
            $table->string('imageProof')->nullable(); 

            $table->timestamps();
        });

        // 5. Payments (Financial)
        Schema::create('payments', function (Blueprint $table) {
            $table->id('paymentID'); // custom PK
            $table->foreignId('assignmentID')->constrained('assignments', 'assignmentID')->onDelete('cascade');
            $table->decimal('amount', 10, 2); 
            $table->string('paymentStatus')->default('pending'); 
            $table->string('paymentProof')->nullable(); 
            $table->dateTime('paymentDate')->nullable(); 
            $table->timestamps();
        });

        // 6. Performance Reviews (Feedback)
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id('reviewID'); // custom PK
            $table->foreignId('userID')->constrained('users', 'userID')->onDelete('cascade');
            $table->integer('rating'); 
            $table->text('comments')->nullable(); 
            $table->dateTime('dateSubmitted')->useCurrent(); 
            $table->timestamps();
        });

        // 7. Leaves (Availability Constraint)
        Schema::create('leaves', function (Blueprint $table) {
            $table->id('leaveID'); // custom PK
            $table->foreignId('userID')->constrained('users', 'userID')->onDelete('cascade'); 
            $table->date('startDate'); 
            $table->date('endDate'); 
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
            $table->foreignId('user_id')->nullable()->index(); // standard session table usually keeps strict convention or fails? 
            // Auth::id() will return userID. 
            // Session driver might expect user_id. I will keep user_id here to be safe with framework internals, 
            // unless I want to change 'user_id' -> 'userID' here too? safer to keep user_id for built-in session.
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('events');
        Schema::dropIfExists('event_rules');
        Schema::dropIfExists('facilitator_skills');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::enableForeignKeyConstraints();
    }
};
