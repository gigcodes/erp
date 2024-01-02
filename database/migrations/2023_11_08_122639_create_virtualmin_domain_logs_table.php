<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualminDomainLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtualmin_domain_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->nullable();
            $table->text('url')->nullable();
            $table->string('command')->nullable();
            $table->string('job_id')->nullable();
            $table->string('status')->nullable();
            $table->text('response')->nullable();
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
        Schema::dropIfExists('virtualmin_domain_logs');
    }
}
