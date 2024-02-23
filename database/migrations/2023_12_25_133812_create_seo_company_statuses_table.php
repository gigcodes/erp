<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoCompanyStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_company_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('seo_company_statuses')->insert([
            'status_name' => 'pending',
            'status_color' => '',
        ]);

        \Illuminate\Support\Facades\DB::table('seo_company_statuses')->insert([
            'status_name' => 'approved',
            'status_color' => '',
        ]);

        \Illuminate\Support\Facades\DB::table('seo_company_statuses')->insert([
            'status_name' => 'rejected',
            'status_color' => '',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_company_statuses');
    }
}
