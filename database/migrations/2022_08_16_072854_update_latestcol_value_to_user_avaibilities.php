<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLatestcolValueToUserAvaibilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_avaibilities', function (Blueprint $table) {
            $ids = \DB::select('SELECT MAX(id) AS id FROM user_avaibilities GROUP BY user_id;');
            foreach ($ids as $key => $value) {
                $ids[$key] = $value->id;
            }
            if (! $ids) {
                $ids = [0];
            }
            \DB::statement('UPDATE `user_avaibilities` SET is_latest = 1 WHERE 1 AND id IN ('.implode(',', $ids).')');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_avaibilities', function (Blueprint $table) {
            \DB::statement('UPDATE `user_avaibilities` SET is_latest = 0 WHERE 1');
        });
    }
}
