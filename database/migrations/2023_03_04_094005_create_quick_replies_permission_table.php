<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuickRepliesPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quick_replies_permission', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('lang_id');
            $table->string('action');
            $table->timestamps();
        });
        Schema::table('translate_replies', function (Blueprint $table) {
            $table->string('status')->nullable()->after('translate_text');
            $table->integer('updated_by_user_id')->nullable()->after('status');
            $table->integer('approved_by_user_id')->nullable()->after('updated_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quick_replies_permission');
    }
}
