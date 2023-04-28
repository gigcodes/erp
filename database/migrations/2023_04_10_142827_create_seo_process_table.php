<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_process', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('website_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->double('price')->nullable();
            $table->boolean('is_price_approved')->default(0)->nullable();
            $table->text('google_doc_link')->nullable();
            $table->bigInteger('seo_process_status_id')->nullable();
            $table->string('live_status_link')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->enum('status', ['planned', 'admin_approve'])->default('planned');
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
        Schema::dropIfExists('seo_process');
    }
}
