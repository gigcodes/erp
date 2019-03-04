<?php

namespace App\Jobs;

use App\Customer;
use App\ChatMessage;
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

    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $user_id, Customer $customer, array $content)
    {
      $this->user_id = $user_id;
      $this->customer = $customer;
      $this->content = $content;
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
        'approved'    => 1,
        'status'      => 2,
      ];

      if ($this->content['message']) {
        $params['message'] = $this->content['message'];
        $message = $this->content['message'];

        app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->customer->phone, NULL, $message, false);
      }

      $chat_message = ChatMessage::create($params);

      if ($this->content['image']) {
        foreach ($this->content['image'] as $image) {
          $chat_message->attachMedia($image['key'], config('constants.media_tags'));

          app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->customer->phone, NULL, $image['url'], false);
        }

        // $chat_message->update(['media_url' => $this->content['image']['url']]);
        // $message = $this->content['image']['url'];
      }
    }
}
