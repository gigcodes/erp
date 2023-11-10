<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSonarQubesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sonar_qubes', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();
            $table->string('rule')->nullable();
            $table->string('severity')->nullable();
            $table->longText('component')->nullable();
            $table->string('project')->nullable();
            $table->string('hash')->nullable();
            $table->longText('textRange')->nullable();
            $table->longText('flows')->nullable();
            $table->string('resolution')->nullable();
            $table->string('status')->nullable();
            $table->longText('message')->nullable();
            $table->string('effort')->nullable();
            $table->string('debt')->nullable();
            $table->string('author')->nullable();
            $table->longText('tags')->nullable();
            $table->string('creationDate')->nullable();
            $table->string('updateDate')->nullable();
            $table->string('closeDate')->nullable();
            $table->string('type')->nullable();
            $table->string('scope')->nullable();
            $table->longText('quickFixAvailable')->nullable();
            $table->longText('messageFormattings')->nullable();
            $table->string('codeVariants')->nullable();
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
        Schema::dropIfExists('sonar_qubes');
    }
}