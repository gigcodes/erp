<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_alias')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('seo_statuses')->insert([
            'status_name' => 'Planned',
            'status_alias' => 'planned',
        ]);

        \Illuminate\Support\Facades\DB::table('seo_statuses')->insert([
            'status_name' => 'Admin Approved',
            'status_alias' => 'admin_approve',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_statuses');
    }
}
