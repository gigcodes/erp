<?php

namespace Database\Seeders;

use App\ModelColor;
use Illuminate\Database\Seeder;

class InsertModelColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $filesInFolder = \File::files('app');
        //$modelArr = [];
        foreach ($filesInFolder as $filesArr) {
            $file = pathinfo($filesArr);
            //array_push($modelArr,['model_name' => $file['filename'],'color_code' => '#ffffff']);
            ModelColor::create(['model_name' => $file['filename'], 'color_code' => '#ffffff']);
        }
    }
}
