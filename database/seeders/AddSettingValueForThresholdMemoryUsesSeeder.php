<?php

namespace Database\Seeders;

use App\Setting;
use App\MemoryUsage;
use Illuminate\Database\Seeder;

class AddSettingValueForThresholdMemoryUsesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::updateOrCreate([
            'name' => 'thresold_limit_for_memory_uses',
        ], [
            'val' => 80,
            'type' => 'number',
        ]);

        // MemoryUsage::create([

        //     'total'=>2000,
        //     'used'=>1000,
        //     'free'=>2000-1800,
        //     'buff_cache'=>100,
        //     'available'=>300

        // ]);

        // to add data

        // $arr = [2000,4000,8000,16000,32000];
        // $free_r = [0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9];

        // for ($i = 0; $i < 10000; $i++) {
        //      $total = $arr[array_rand($arr,1)];
        //      $free_ram  =$free_r[array_rand($free_r,1)] * $total;
        //      $uses = $total - $free_ram;
        //      $buff_cache = (random_int(1,5)/100)*$total;
        //      $available  = $total - $uses - $buff_cache;

        //     MemoryUsage::create([
        //         'total'=>$total,
        //             'used'=>$uses,
        //             'free'=>$free_ram,
        //             'buff_cache'=>$buff_cache,
        //             'available'=>$available

        //     ]);

        // }
    }
}
