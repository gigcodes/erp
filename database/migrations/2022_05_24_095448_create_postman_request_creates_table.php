<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostmanRequestCreatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postman_request_creates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('folder_name')->nullable();
            $table->string('request_name')->nullable();
            $table->string('request_type')->nullable();
            $table->string('request_url')->nullable();
            $table->text('params')->nullable();
            $table->string('authorization_type')->nullable();
            $table->longText('authorization_token')->nullable();
            $table->longText('request_headers')->nullable();
            $table->text('body_type')->nullable();
            $table->longText('body_json')->nullable();
            $table->longText('pre_request_script')->nullable();
            $table->longText('tests')->nullable();
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
        Schema::dropIfExists('postman_request_creates');
    }
}
