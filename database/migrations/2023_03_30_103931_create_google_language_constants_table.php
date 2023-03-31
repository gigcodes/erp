<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleLanguageConstantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_language_constants', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('google_language_constant_id');
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->boolean('is_targetable')->default(false);
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
        Schema::dropIfExists('google_language_constants');
    }
}
