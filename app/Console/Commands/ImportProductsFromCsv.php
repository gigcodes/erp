<?php

namespace App\Console\Commands;

use App\Imports\ProductsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Excel;

class ImportProductsFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products-from-csv';

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
        (new ProductsImport)->import(__DIR__ . '/products.csv', null, Excel::CSV);
    }
}
