<?php

namespace App\Jobs;

use App\Customer;
use App\ChatMessage;
use App\MessageQueue;
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
        'status'      => 8,
      ];

      if (is_numeric($this->customer->phone)) {
        $send_number = $this->customer->whatsapp_number ?? NULL;

        if ($this->content['message']) {
          $params['message'] = $this->content['message'];
          $message = $this->content['message'];

          $chat_message = ChatMessage::create($params);

          app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->customer->phone, $send_number, $message, false, $chat_message->id);
        }

        if (isset($this->content['image'])) {
          if (!isset($chat_message)) {
            $chat_message = ChatMessage::create($params);
          }

          foreach ($this->content['image'] as $image) {
            $chat_message->attachMedia($image['key'], config('constants.media_tags'));

            app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->customer->phone, $send_number, str_replace(' ', '%20', $image['url']), false, $chat_message->id);
          }
        }

        $chat_message->update([
          'approved'  => 1
        ]);

        $message_queue = MessageQueue::find($this->message_queue_id);
        $message_queue->chat_message_id = $chat_message->id;
        $message_queue->sent = 1;
        $message_queue->save();
      }
    }
}
