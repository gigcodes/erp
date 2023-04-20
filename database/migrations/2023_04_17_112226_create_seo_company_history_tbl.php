<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeoCompanyHistoryTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_company_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('seo_company_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('company_type_id')->nullable();
            $table->bigInteger('website_id')->nullable();
            $table->bigInteger('email_address_id')->nullable();
            $table->string('da')->nullable();
            $table->string('pa')->nullable();
            $table->string('ss')->nullable();
            $table->text('live_link')->nullable();
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
        Schema::dropIfExists('seo_company_histories');
    }
}
