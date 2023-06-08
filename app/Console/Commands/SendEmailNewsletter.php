<?php

namespace App\Console\Commands;

use App\Newsletter;
use Illuminate\Console\Command;
use App\LogRequest;

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
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $newsletters = Newsletter::whereNull('sent_on')->where('sent_at', '!=', '')->get();
        //$newsletters = Newsletter::where("id",2)->get();

        foreach ($newsletters as $newsletter) {
            //$template = \App\MailinglistTemplate::getNewsletterTemplate($newsletter->store_website_id);
            $template = $newsletter->mailinglistTemplate;
            if ($template) {
                $products = $newsletter->products;

                if (! $products->isEmpty()) {
                    foreach ($products as $product) {
                        if ($product->hasMedia(config('constants.attach_image_tag'))) {
                            foreach ($product->getMedia(config('constants.attach_image_tag')) as $image) {
                                $product->images[] = $image->getUrl();
                            }
                        }
                    }
                }

                $mailinglist = $newsletter->mailinglist;

                if (! empty($mailinglist) && $mailinglist->remote_id) {
                    if ($mailinglist->service && isset($mailinglist->service->name)) {
                        if ($mailinglist->service->name == 'AcelleMail') {
                            $curl = curl_init();
                            $url = 'http://165.232.42.174/api/v1/campaign/create/' . $mailinglist->remote_id . '?api_token=' . config('env.ACELLE_MAIL_API_TOKEN');

                            curl_setopt_array($curl, [
                                //   CURLOPT_URL => "http://165.232.42.174/api/v1/campaign/create/".$mailinglist->remote_id."?api_token=".getenv('ACELLE_MAIL_API_TOKEN'),
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => ['name' => $template->subject, 'subject' => $template->subject, 'run_at' => $newsletter->sent_at, 'template_content' => view($template->mail_tpl, compact('products', 'newsletter'))],
                            ]);

                            $response = curl_exec($curl);
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            $parameters = []; 
                            LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($response), $httpcode, \App\Console\Commands\SendEmailNewsletter::class, 'handle');

                            $response = json_decode($response);
                            $newsletter->sent_on = date('Y-m-d H:i:s');
                            $newsletter->save();
                        }
                    }
                }
            }
        }
    }
}
