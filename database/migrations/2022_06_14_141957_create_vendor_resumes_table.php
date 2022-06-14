<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('first_name')->nullable();
            $table->string('second_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->text('career_objective')->nullable();
            $table->longText('work_experiance')->nullable();
            $table->longText('reason_for_leaving')->nullable();
            $table->longText('date_from')->nullable();
            $table->longText('date_to')->nullable();
            $table->longText('destination')->nullable();
            $table->longText('organization')->nullable();
            $table->longText('project')->nullable();
            $table->longText('dev_role')->nullable();
            $table->longText('tools')->nullable();
            $table->string('soft_framework')->nullable();
            $table->string('soft_description')->nullable();
            $table->string('soft_experience')->nullable();
            $table->string('soft_remark')->nullable();
            $table->string('father_name')->nullable();
            $table->string('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('langauge_know')->nullable();
            $table->string('hobbies')->nullable();
            $table->text('address')->nullable();
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
