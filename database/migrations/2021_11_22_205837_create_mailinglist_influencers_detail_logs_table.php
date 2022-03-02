<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
 
class CreateMailinglistInfluencersDetailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailinglist_iInfluencers_detail_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mailinglist_iInfluencers_log_id');
            $table->string('service');
            $table->string('maillist_id');
            $table->string('email');
            $table->string('name');
            $table->string('url');
            $table->text('request_data');
            $table->text('response_data');
            $table->text('message');
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
        Schema::dropIfExists('mailinglist_iInfluencers_detail_logs');
    }
}
