<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostmanEditHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postman_edit_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("postman_request_id")->nullable();
            $table->integer("user_id")->nullable();
            $table->string("user_ids")->nullable();
            $table->string("user_permission")->nullable();
            $table->string('folder_name')->nullable();
            $table->string('request_name')->nullable();
            $table->string('request_type')->nullable();
            $table->text('request_url')->nullable();
            $table->string('controller_name')->nullable();
            $table->string('model_name')->nullable();
            $table->string('method_name')->nullable();
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('postman_edit_histories');
    }
}
