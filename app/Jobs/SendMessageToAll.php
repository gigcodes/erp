<?php

namespace App\Jobs;

use App\Customer;
use App\ChatMessage;
use App\MessageQueue;
use App\BroadcastImage;
use App\Product;
use Carbon\Carbon;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMessageToAll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $customer;
    protected $content;
    protected $message_queue_id;

    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $user_id, Customer $customer, array $content, int $message_queue_id)
    {
      $this->user_id = $user_id;
      $this->customer = $customer;
      $this->content = $content;
      $this->message_queue_id = $message_queue_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $params = [
        'number'      => NULL,
        'user_id'     => $this->user_id,
        'customer_id' => $this->customer->id,
        'approved'    => 0,
        'status'      => 8, // status for Broadcast messages
      ];

      if (is_numeric($this->customer->phone)) {
        $send_number = $this->customer->whatsapp_number ?? NULL;

        if (array_key_exists('linked_images', $this->content)) {
          $chat_message = ChatMessage::create($params);

          foreach ($this->content['linked_images'] as $image) {

            if (is_array($image)) {
              $image_key = $image['key'];
              $mediable_type = "BroadcastImage";

              $broadcast = BroadcastImage::with('Media')
              ->whereRaw("broadcast_images.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
              ->first();

              $product_ids = json_decode($broadcast->products, true);
            } else {
              $broadcast_image = BroadcastImage::find($image);
              // dump($broadcast_image);
              $product_ids = json_decode($broadcast_image->products, true);
            }



            // $product_img = $product_image->getMedia(config('constanst.media_tags'))->first();
            // $chat_message->attachMedia($product_img, config('constants.media_tags'));

            foreach ($product_ids as $product_id) {
              $product = Product::find($product_id);

              if ($product->hasMedia(config('constants.media_tags'))) {
                $chat_message->attachMedia($product->getMedia(config('constants.media_tags'))->first()->getKey(), config('constants.media_tags'));
              }
            }
          }
        }

        if ($this->content['message']) {
          $params['message'] = $this->content['message'];
          $message = $this->content['message'];

          $chat_message = ChatMessage::create($params);

          try {
            if ($send_number == '919152731483') {
              dump('sending message with NEW API');
              app('App\Http\Controllers\WhatsAppController')->sendWithNewApi($this->customer->phone, $send_number, $message, NULL, $chat_message->id);
            } else {
              app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->customer->phone, $send_number, $message, false, $chat_message->id);
            }
          } catch (\Exception $e) {

          }
        }

        if (array_key_exists('linked_images', $this->content)) {
          $chat_message = ChatMessage::create($params);

          foreach ($this->content['linked_images'] as $image) {
            if (is_array($image)) {
              $chat_message->attachMedia($image['key'], config('constants.media_tags'));

              try {
                if ($send_number == '919152731483') {
                  dump('sending linked images with NEW API');
                  app('App\Http\Controllers\WhatsAppController')->sendWithNewApi($this->customer->phone, $send_number, NULL, str_replace(' ', '%20', $image['url']), $chat_message->id);
                } else {
                  app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->customer->phone, $send_number, str_replace(' ', '%20', $image['url']), false, $chat_message->id);
                }
              } catch (\Exception $e) {

              }
            } else {
              $broadcast_image = BroadcastImage::find($image);

              if ($broadcast_image->hasMedia(config('constants.media_tags'))) {
                foreach ($broadcast_image->getMedia(config('constants.media_tags')) as $brod_image) {
                  $chat_message->attachMedia($brod_image, config('constants.media_tags'));

                  try {
                    if ($send_number == '919152731483') {
                      dump('sending images with NEW API');
                      app('App\Http\Controllers\WhatsAppController')->sendWithNewApi($this->customer->phone, $send_number, NULL, str_replace(' ', '%20', $brod_image->getUrl()), $chat_message->id);
                    } else {
                      app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->customer->phone, $send_number, str_replace(' ', '%20', $brod_image->getUrl()), false, $chat_message->id);
                    }
                  } catch (\Exception $e) {

                  }
                }
              }
            }


          }
        }

        if (isset($this->content['image'])) {
          if (!isset($chat_message)) {
            $chat_message = ChatMessage::create($params);
          }

          foreach ($this->content['image'] as $image) {
            $chat_message->attachMedia($image['key'], config('constants.media_tags'));

            try {
              if ($send_number == '919152731483') {
                app('App\Http\Controllers\WhatsAppController')->sendWithNewApi($this->customer->phone, $send_number, NULL, str_replace(' ', '%20', $image['url']), $chat_message->id);
              } else {
                app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->customer->phone, $send_number, str_replace(' ', '%20', $image['url']), false, $chat_message->id);
              }
            } catch (\Exception $e) {

            }
          }
        }

        $chat_message->update([
          'approved'  => 1
        ]);

        $message_queue = MessageQueue::find($this->message_queue_id);
        $message_queue->chat_message_id = $chat_message->id;
        $message_queue->sent = 1;
        $message_queue->save();
      } else {
        MessageQueue::where('customer_id', $this->customer->id)->delete();
      }
    }
}
