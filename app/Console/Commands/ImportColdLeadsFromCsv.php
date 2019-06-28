<?php

namespace App\Console\Commands;

use App\Imports\ColdLeadsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Excel;

class ImportColdLeadsFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cold-leads';

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
        (new ColdLeadsImport())->import(__DIR__ . '/leads.csv', null, Excel::CSV);

    }
}
