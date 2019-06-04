<?php

namespace App\Console\Commands;

use App\Designer;
use App\Supplier;
use Illuminate\Console\Command;

class ImportFarfetchSupplier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'farfetch:import-suppliers';

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
        $suppliers = Designer::all();

        foreach ($suppliers as $supplier) {
            $existingSupplier = Supplier::where('supplier', $supplier->title)->first();

            if ($existingSupplier) {
                $existingSupplier->brands = $supplier->designers;
                $existingSupplier->save();
                continue;
            }

            $existingSupplier = new Supplier();
            $existingSupplier->source = $supplier->website;
            $existingSupplier->supplier = $supplier->title;
            $existingSupplier->brands = $supplier->designers;
            $existingSupplier->address = $supplier->address;
            $existingSupplier->save();
        }
    }
}
