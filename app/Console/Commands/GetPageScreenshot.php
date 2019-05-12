<?php

namespace App\Console\Commands;

use App\PageScreenshots;
use App\Services\Bots\Screenshot;
use Illuminate\Console\Command;

class GetPageScreenshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'screenshot:sites';

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
        $sites = PageScreenshots::where('image_link', '')->get();

        $duskShell = new Screenshot();
        $duskShell->prepare();

        foreach ($sites as $site) {
            $duskShell->emulate($this, $site, '');
        }
    }
}
