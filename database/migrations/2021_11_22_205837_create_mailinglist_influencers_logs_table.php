<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
 
class CreateMailinglistInfluencersLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailinglist_iInfluencers_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('service');
            $table->string('maillist_id');
            $table->string('email');
            $table->string('name');
            $table->string('url');
            $table->text('request_data');
            $table->text('response_data');
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
        Schema::dropIfExists('mailinglist_iInfluencers_logs');
    }
}
