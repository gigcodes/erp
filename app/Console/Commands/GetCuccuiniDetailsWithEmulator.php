<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use Illuminate\Console\Command;
use App\Services\Bots\CucProductDataEmulator;
use App\Services\Bots\CucProductExistsEmulator;

class GetCuccuiniDetailsWithEmulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cuccu:get-product-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $country;

    protected $IP;

    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
            if (strpos($letters, 'C') === false) {
                return;
            }

            $this->authenticate();

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function authenticate()
    {
        $url = 'https://shop.cuccuini.it/it/register.html';

        $duskShell = new CucProductDataEmulator();
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '');
        } catch (Exception $exception) {
            $content = ['', ''];
        }
    }

    public function doesProductExist($product)
    {
        $url = 'https://shop.cuccuini.it/it/register.html';

        $duskShell = new CucProductExistsEmulator();
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '', $product);
        } catch (Exception $exception) {
            $content = false;
        }

        return $content;
    }

    private function setCountry(): void
    {
        $this->country = 'IT';
    }
}
