<?php

namespace App\Console\Commands;

use ColorThief\ColorThief;
use Grafika\Gd\Image;
use Grafika\Grafika;
use Illuminate\Console\Command;

class GraphicaImageCropper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crop:using-graphica';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $domC = ColorThief::getColor(__DIR__ . '/image.jpg');
        dd($domC);
    }


}
