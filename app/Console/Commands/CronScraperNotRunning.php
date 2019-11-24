<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\CronJobReport;
use Carbon\Carbon;

class CronScraperNotRunning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:not-running';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message to admin if scraper is not running.';

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
        return;
        // Create cron job report
        $report = CronJobReport::create([
            'signature' => $this->signature,
            'start_time' => Carbon::now()
        ]);

        // Get all suppliers
        $sql = "
            SELECT
                s.id,
                s.supplier,
                s.scraper_name,
                MAX(ls.updated_at) AS last_update,
                s.scraper_name,
                s.inventory_lifetime 
            FROM
                suppliers s
            LEFT JOIN 
                log_scraper ls 
            ON 
                ls.website=s.scraper_name
            WHERE
                s.supplier_status_id=1 
            GROUP BY 
                s.id 
            HAVING
                last_update < DATE_SUB(NOW(), INTERVAL s.inventory_lifetime DAY) OR 
                last_update IS NULL
            ORDER BY 
                s.supplier
        ";
        $allSuppliers = DB::select($sql);

        // Do we have results?
        if (count($allSuppliers) > 0) {
            // Loop over suppliers
            foreach ($allSuppliers as $supplier) {
                // Create message
                $message = '[' . date('d-m-Y H:i:s') . '] Scraper not running: ' . $supplier->supplier;

                // Output debug message
                dump("Scraper not running: " . $supplier->supplier);

                // Try to send message
                try {
                    // Output debug message
                    dump("Sending message");

                    // Send message
                    app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi('34666805119', '971502609192', $message);
                    app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi('919004780634', '971502609192', $message);
                } catch (\Exception $e) {
                    // Output error
                    dump($e->getMessage());
                }
            }
        }
    }
}
