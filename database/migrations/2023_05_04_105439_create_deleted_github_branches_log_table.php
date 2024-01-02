<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeletedGithubBranchesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deleted_github_branches_log', function (Blueprint $table) {
            $table->id();
            $table->integer('deleted_by');
            $table->string('branch_name');
            $table->integer('repository_id');
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->timestamp('deleted_at');
            $table->timestamps();

            $table->index('deleted_by');
            $table->index('repository_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deleted_github_branches_log');
    }
}
