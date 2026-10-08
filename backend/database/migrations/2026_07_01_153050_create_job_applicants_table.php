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
        Schema::create('job_applicants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->index();
            
            // System Fields
            $table->string('applicant_id')->unique()->nullable(); // e.g. APP-0001
            $table->dateTime('application_date')->nullable();
            $table->string('status')->default('Active'); // Active/Inactive
            $table->text('notes')->nullable();

            // Personal Information
            $table->string('full_name');
            $table->string('email_address')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('alternate_mobile_number')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('nationality')->nullable();
            $table->string('aadhaar_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('profile_photo')->nullable();

            // Address Information
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('pincode')->nullable();

            // Job Information
            $table->string('applied_position')->nullable();
            $table->string('preferred_department')->nullable();
            $table->string('employment_type')->nullable(); // Full-Time, Part-Time, etc.
            $table->string('preferred_location')->nullable();
            $table->date('expected_joining_date')->nullable();
            $table->string('notice_period')->nullable();
            $table->string('current_employment_status')->nullable();
            $table->string('total_experience')->nullable();
            $table->string('relevant_experience')->nullable();
            $table->string('current_company_name')->nullable();
            $table->string('last_company_name')->nullable();
            $table->string('current_designation')->nullable();
            $table->string('last_designation')->nullable();
            $table->decimal('current_salary', 15, 2)->nullable();
            $table->decimal('expected_salary', 15, 2)->nullable();
            $table->date('last_working_date')->nullable();
            $table->text('reason_for_leaving')->nullable();

            // JSON Arrays for Multi-row data
            $table->json('education_details')->nullable();
            $table->json('experience_details')->nullable();

            // Skills
            $table->text('technical_skills')->nullable();
            $table->text('soft_skills')->nullable();
            $table->text('certifications')->nullable();
            $table->text('languages_known')->nullable();

            // Documents (Paths)
            $table->string('resume')->nullable();
            $table->string('cover_letter')->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('aadhaar_card_doc')->nullable();
            $table->string('pan_card_doc')->nullable();
            $table->string('experience_letter')->nullable();
            $table->string('salary_slips')->nullable();
            $table->string('educational_certificates')->nullable();
            $table->string('portfolio_doc')->nullable();

            // Additional Information
            $table->text('hobbies')->nullable();
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('achievements')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('github_profile')->nullable();
            $table->string('portfolio_website')->nullable();
            $table->string('reference_name')->nullable();
            $table->string('reference_contact')->nullable();
            $table->string('reference_email')->nullable();
            $table->string('source')->nullable(); // How did you hear about us?

            // HR Details
            $table->string('interview_status')->nullable();
            $table->date('interview_date')->nullable();
            $table->time('interview_time')->nullable();
            $table->string('interview_mode')->nullable(); // Online/Offline
            $table->string('interviewer_name')->nullable();
            $table->text('hr_remarks')->nullable();
            $table->string('candidate_status')->default('Applied'); // Applied, Shortlisted, etc.
            $table->string('offer_letter_status')->nullable();
            $table->string('joining_status')->nullable();
            $table->string('employee_id')->nullable();

            // Tracking
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign Keys (if applicable, but in this architecture mostly handled by app logic)
            // $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applicants');
    }
};
