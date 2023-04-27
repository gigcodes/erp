<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateMarketingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('affiliate_marketing_logs')) {
            Schema::create('affiliate_marketing_logs', function (Blueprint $table) {
                $table->id();
                $table->string('user_name', 191)->nullable();
                $table->string('name', 191)->nullable();
                $table->string('status', 191)->nullable();
                $table->text('message');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliate_marketing_logs');
    }
}
