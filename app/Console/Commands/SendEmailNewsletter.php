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
        
        $newsletters = Newsletter::all();
        foreach($newsletters as $newsletter) {
            $template = \App\MailinglistTemplate::getNewsletterTemplate($newsletter->store_website_id);
            if ($template) {
                $products = $newsletter->products;
                if (!$products->isEmpty()) {
                    foreach ($products as $product) {
                        if ($product->hasMedia(config('constants.attach_image_tag'))) {
                            foreach ($product->getMedia(config('constants.attach_image_tag')) as $image) {
                                $product->images[] = $image->getUrl();
                            }
                        }
                    }
                }
                
                echo view($template->mail_tpl, compact('products', 'newsletter'));
            }
        }
    }
}
