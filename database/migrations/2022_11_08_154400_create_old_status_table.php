<?php

use App\OldStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOldStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
            $table->timestamps();
        });

        // add existing status to table.
        $status = [
            'pending',
            'disputed',
            'settled',
            'paid',
            'closed',
        ];

        $rows = [];

        foreach ($status as $key => $value) {
            $rows[] = ['status' => $value, 'created_at' => now(), 'updated_at' => now()];
        }

        OldStatus::insert($rows);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('old_status');
    }
}
