<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StoreViewsGtMetrixUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_views_gt_metrix_url', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('account_id')->nullable();
			$table->integer('store_view_id')->nullable();
			$table->string('store_name')->nullable();
            $table->text('website_url')->nullable();
			$table->integer('process')->nullable();
            $table->timestamps();
        });
    }

    }
