<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FilterCompositionSpace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'composition:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Filter composition with space';

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
        //
        \Log::info('Non breaking space issue started =>' . date('Y-m-d H:i:s'));
        /*$compositions = \App\Compositions::all();
        if (! $compositions->isEmpty()) {
            foreach ($compositions as $composition) {
                $str = str_replace(' ', ' ', $composition->name);
                $composition->name = $str;
                $composition->save();
            }
        }*/

        \Log::info('Non breaking space issue has been done =>' . date('Y-m-d H:i:s'));
    }
}
