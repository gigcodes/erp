<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\Product;
use App\ChatMessage;

class AutoInterestMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:image-interest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a message to a customer with latest interested products';

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
      $params = [
        'number'  => NULL,
        'status'  => 7, // message status for auto messaging
        'user_id' => 6,
        'message' => 'Auto attached images'
      ];

      $customers = Customer::with(['Leads' => function ($query) {
        $query->whereNotNull('brand')->orWhereNotNull('multi_category')->latest();
      }])->whereHas('Leads', function($query) {
        $query->whereNotNull('brand')->orWhereNotNull('multi_category');
      })->get()->toArray();

      foreach ($customers as $customer) {
        $category = json_decode($customer['leads'][0]['multi_category']);
        $products = Product::where('brand', $customer['leads'][0]['brand'])->where('category', (int) $category[0])->latest()->take(20)->get();

        if (count($products) > 0) {
          $params['customer_id'] = $customer['id'];

          $chat_message = ChatMessage::create($params);

          foreach ($products as $product) {
            $chat_message->attachMedia($product->getMedia(config('constants.media_tags'))->first(), config('constants.media_tags'));
          }
        }
      }
    }
}
