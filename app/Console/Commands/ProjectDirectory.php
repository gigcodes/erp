<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ProjectFileManager;
use DB;

class ProjectDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project_directory:manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It go through with all directories and update to db';

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
        $cc = app()->make('App\Http\Controllers\ProjectFileManagerController');
		app()->call([$cc, 'listTree'], []);
	}
}
