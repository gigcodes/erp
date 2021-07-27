<?php

use App\WebsiteProductCsv;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsiteProductCsvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_product_csvs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('path');
            $table->timestamps();
        });

       DB::table('website_product_csvs')->insert([
            [
            'name'=>'sololuxury',
            'path'=>'https://www.sololuxury.com/var/exportcsv/product.csv'
            ],
            [
            'name'=>'suvandnat',
            'path'=>'https://www.suvandnat.com/var/exportcsv/product.csv'
            ],
            [
            'name'=>'Avoir-chic.com',
            'path'=>'https://www.Avoir-chic.com/var/exportcsv/product.csv'    
            ],
            [
            'name'=>'veralusso',
            'path'=>'https://www.veralusso.com/var/exportcsv/product.csv'
            ],
            [
            'name'=>'Brands-labels',
            'path'=>'https://www.Brands-labels.com/var/exportcsv/product.csv'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_product_csvs');
    }
}
