<?php
namespace Database\Seeders;

use App\CronStatus;
use App\Http\Controllers\Cron\ShowMagentoCronDataController;
use App\SendgridEventColor;
use Illuminate\Database\Seeder;

class SendgridEventColorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $lists = [
            "processed",
            "dropped",
            "deferred",
            "bounced",
            "delivered",
            "opened",
            "open",
            "clicked",
            "unsubscribed",
            "spam reports",
            "group unsubscribed",
            "group resubscribes"
        ];

        foreach($lists as $list)
        {
            SendgridEventColor::firstOrCreate([
                'name' => $list
            ]);
        }
    }
}
