<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendEmailNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send newsletter';

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
        
        $products = [
            ["id" => 1 , "name" => "Some name 1", "price" => "$10"],
            ["id" => 1 , "name" => "Some name 2", "price" => "$10"],
            ["id" => 1 , "name" => "Some name 3", "price" => "$10"],
            ["id" => 1 , "name" => "Some name 4", "price" => "$10"],
            ["id" => 1 , "name" => "Some name 5", "price" => "$10"],
            ["id" => 1 , "name" => "Some name 6", "price" => "$10"],
        ];

        echo view('maileclipse::templates.sendingLandingPage', compact('products'));
    }
}
