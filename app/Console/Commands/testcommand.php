<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class testcommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shyam:name';

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
        $email = \App\Email::create([
            'model_id' => 1,
            'model_type' => 'User',
            'from' => 'shyam@ghanshyamdigital.com',
            'to' => 'shyam@ghanshyamdigital.com',
            'subject' => 'shyam@ghanshyamdigital.com',
            'message' => 'shyam@ghanshyamdigital.com',
            'template' => 'referr-coupon',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
            'is_draft' => 1,
        ]);

        \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
    }
}
