<?php

namespace App\Console\Commands;

use App\Customer;
use Carbon\Carbon;
use App\CronJobReport;
use Illuminate\Console\Command;

class SyncInstagramMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:instagram-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Instagram Direct Messaging With Customers Page';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $messages;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $inbox = $this->messages->getInbox()->asArray();
            if (isset($inbox['inbox']['threads'])) {
                $threads = $inbox['inbox']['threads'];
                foreach ($threads as $thread) {
                    $user = $thread['users'];
                    if (count($user) !== 1) {
                        continue;
                    }
                    echo $user[0]['username'] . "\n";
                    $customer = $this->createCustomer($user[0]);

                    if ($customer) {
                        $this->createThread($customer, $thread);
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    /**
     * @param mixed $user
     *
     * @return Customer|void
     */
    private function createCustomer($user)
    {
        $customer = Customer::where('instahandler', $user['pk'])->first();
        if ($customer) {
            return;
        }

        $customer = Customer::where('ig_username', $user['username'])->first();

        if (! $customer) {
            $customer       = new Customer();
            $customer->name = $user['full_name'];
        }

        $customer->instahandler = $user['pk'];
        $customer->ig_username  = $user['username'];
        $customer->save();

        return $customer;
    }

    private function createThread($customer, $t)
    {
    }
}
