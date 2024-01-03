<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetManagerTerminalUserAccessRemakrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_manager_terminal_user_access_remakrs', function (Blueprint $table) {
            $table->id();
            $table->integer('amtua_id')->default(0);
            $table->text('remarks')->nullable();
            $table->integer('added_by')->default(0);
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
        Schema::dropIfExists('asset_manager_terminal_user_access_remakrs');
    }
}
