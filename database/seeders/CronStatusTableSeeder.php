<?php
namespace Database\Seeders;

use App\CronStatus;
use App\Http\Controllers\Cron\ShowMagentoCronDataController;
use Illuminate\Database\Seeder;

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
        $lists = $magentoCronData->cronStatus();

        foreach($lists as $list)
        {
            CronStatus::firstOrCreate([
                'name' => $list
            ]);
        }
    }
}
