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
        foreach ($filesInFolder as $filesArr) {
            $file = pathinfo($filesArr);
            ModelColor::create(['model_name' => $file['filename'], 'color_code' => '#ffffff']);
        }
    }
}
