<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plan_action')->nullable();
            $table->integer('plan_id');
            $table->integer('plan_action_type')->comment('1:Strength 2:Weakness 3:Opportunity 4:Threat');
            $table->integer('created_by')->comment('join with user');
            $table->integer('is_active')->comment('1: active 0:inActive')->default(1);
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
        Schema::dropIfExists('plan_actions');
    }
}
