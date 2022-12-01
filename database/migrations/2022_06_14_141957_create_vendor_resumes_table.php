<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorResumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_resumes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->nullable();
            $table->string('pre_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('second_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->text('career_objective')->nullable();
            $table->string('salary_in_usd')->nullable();
            $table->string('expected_salary_in_usd')->nullable();

            $table->string('preferred_working_hours')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('time_zone')->nullable();
            $table->string('preferred_working_days')->nullable();
            $table->string('start_day')->nullable();
            $table->string('end_day')->nullable();
            $table->longText('full_time')->nullable();
            $table->longText('part_time')->nullable();
            $table->longText('job_responsibilities')->nullable();
            $table->longText('projects_worked')->nullable();
            $table->longText('tool_used')->nullable();
            $table->longText('work_remark')->nullable();
            $table->longText('current_assignments')->nullable();
            $table->longText('current_assignments_description')->nullable();
            $table->longText('current_assignments_hours_utilisted')->nullable();

            $table->longText('work_experiance')->nullable();
            $table->longText('reason_for_leaving')->nullable();
            $table->longText('date_from')->nullable();
            $table->longText('date_to')->nullable();
            $table->longText('designation')->nullable();
            $table->longText('organization')->nullable();
            $table->longText('project')->nullable();
            $table->longText('dev_role')->nullable();
            $table->longText('tools')->nullable();
            $table->string('soft_expertise')->nullable();
            $table->string('soft_framework')->nullable();
            $table->string('soft_proficiency')->nullable();
            $table->string('soft_description')->nullable();
            $table->string('soft_certifications')->nullable();
            $table->string('soft_upload_document')->nullable();
            $table->string('soft_experience')->nullable();
            $table->longText('soft_remark')->nullable();

            $table->longText('edu_date_from')->nullable();
            $table->longText('edu_date_to')->nullable();
            $table->longText('edu_institute_programme')->nullable();
            $table->longText('edu_course_name')->nullable();
            $table->longText('edu_grades')->nullable();
            $table->longText('edu_remark')->nullable();

            $table->string('father_name')->nullable();
            $table->string('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('langauge_know')->nullable();
            $table->string('hobbies')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('pin_code')->nullable();
            $table->longText('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_resumes');
    }
}
