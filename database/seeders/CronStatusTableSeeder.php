<?php

namespace Database\Seeders;

use App\CronStatus;
use Illuminate\Database\Seeder;
use App\Http\Controllers\Cron\ShowMagentoCronDataController;

class CronStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $magentoCronData = new ShowMagentoCronDataController();
        $lists           = $magentoCronData->cronStatus();

        foreach ($lists as $list) {
            CronStatus::firstOrCreate([
                'name' => $list,
            ]);
        }
    }
}
