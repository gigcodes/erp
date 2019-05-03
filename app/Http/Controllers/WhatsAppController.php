<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
use App\Jobs\SendMessageToAll;
use App\Jobs\SendMessageToSelected;
use App\Category;
use App\Notification;
use App\AutoReply;
use App\Leads;
use App\Order;
use App\Status;
use App\Setting;
use App\User;
use App\Brand;
use App\Product;
use App\CommunicationHistory;
use App\ApiKey;
use App\Message;
use App\Instruction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers;
use App\ChatMessage;
use App\PushNotification;
use App\NotificationQueue;
use App\Purchase;
use App\Customer;
use App\MessageQueue;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Imports\CustomerNumberImport;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Illuminate\Support\Facades\DB;
use Validator;
use Image;
use File;

class WhatsAppController extends FindByNumberController
{
    /**
     * Incoming message URL for whatsApp
     *
     * @return \Illuminate\Http\Response
     */
    public function incomingMessage(Request $request)
    {
		$data = $request->json()->all();

    if ($data['event'] == 'INBOX') {
      $to = $data['to'];
  		$from = $data['from'];
  		$text = $data['text'];
  		$lead = $this->findLeadByNumber( $from );
      $user = $this->findUserByNumber($from);
      $purchase = $this->findPurchaseByNumber($from);
      $customer = $this->findCustomerByNumber($from);

      $params = [
        'number' => $from
      ];

      if ($user) {
        $params = $this->modifyParamsWithMessage($params, $data);
        $instruction = Instruction::where('assigned_to', $user->id)->latest()->first();
        $myRequest = new Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add(['remark' => $params['message'], 'id' => $instruction->id, 'module_type' => 'instruction', 'user_name' => "User from Whatsapp"]);

        app('App\Http\Controllers\TaskModuleController')->addRemark($myRequest);

        NotificationQueueController::createNewNotification([
          'message' => $params['message'],
          'timestamps' => ['+0 minutes'],
          'model_type' => Instruction::class,
          'model_id' =>  $instruction->id,
          'user_id' => '6',
          'sent_to' => $instruction->assigned_from,
          'role' => '',
        ]);
      }

      if ($purchase) {
        $params['lead_id'] = null;
        $params['order_id'] = null;
        $params['purchase_id'] = $purchase->id;

        $params = $this->modifyParamsWithMessage($params, $data);
        $message = ChatMessage::create($params);
        $model_type = 'purchase';
        $model_id = $purchase->id;
        $purchase->update([
            'whatsapp_number' => $to
        ]);
      }

      // if ($lead) {
      //   $params['lead_id'] = $lead->id;
      //
      //   if ($lead->customer) {
      //     $params['customer_id'] = $lead->customer->id;
      //   }
      //
      //   $params = $this->modifyParamsWithMessage($params, $data);
      //   $message = ChatMessage::create($params);
      //   $model_type = 'leads';
      //   $model_id = $lead->id;
      //   $lead->update([
      //       'whatsapp_number' => $to
      //   ]);
      // } else {
      //   $order= $this->findOrderByNumber($from);
      //
      //   if ($order) {
      //     $params['lead_id'] = null;
      //     $params['order_id'] = $order->id;
      //
      //     if ($order->customer) {
      //       $params['customer_id'] = $order->customer->id;
      //     }
      //
      //     $params = $this->modifyParamsWithMessage($params, $data);
      //     $message = ChatMessage::create($params);
      //     $model_type = 'order';
      //     $model_id = $order->id;
      //     $order->update([
      //         'whatsapp_number' => $to
      //     ]);
      //   }
      // }

      if ($customer) {
        $params['customer_id'] = $customer->id;

        $params = $this->modifyParamsWithMessage($params, $data);
        $message = ChatMessage::create($params);
        $model_type = 'customers';
        $model_id = $customer->id;
        $customer->update([
          'whatsapp_number' => $to
        ]);

        if (Setting::get('forward_messages') == 1) {
          if (Setting::get('forward_start_date') != null && Setting::get('forward_end_date') != null) {
            $time = Carbon::now();
            $start_date = Carbon::parse(Setting::get('forward_start_date'));
            $end_date = Carbon::parse(Setting::get('forward_end_date'));

            if ($time->between($start_date, $end_date, true)) {
              $forward_users_ids = json_decode(Setting::get('forward_users'));
              $second_message = '';

              if ($message->message == null) {
                $forwarded_message = "FORWARDED from $customer->name";
                $second_message = $message->media_url;
              } else {
                $forwarded_message = "FORWARDED from $customer->name - " . $message->message;
              }

              foreach ($forward_users_ids as $user_id) {
                $user = User::find($user_id);

                $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $forwarded_message, FALSE, $message->id);

                if ($second_message != '') {
                  $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, FALSE, $message->id);
                }
              }
            }
          } else {
            $forward_users_ids = json_decode(Setting::get('forward_users'));
            $second_message = '';

            if ($message->message == null) {
              $forwarded_message = "FORWARDED from $customer->name";
              $second_message = $message->media_url;
            } else {
              $forwarded_message = "FORWARDED from $customer->name - " . $message->message;
            }

            foreach ($forward_users_ids as $user_id) {
              $user = User::find($user_id);

              $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $forwarded_message, FALSE, $message->id);

              if ($second_message != '') {
                $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, FALSE, $message->id);
              }
            }
          }
        }

        // Auto DND
        if (array_key_exists('message', $params) && strtoupper($params['message']) == 'DND') {
          if ($customer = Customer::find($params['customer_id'])) {
            $customer->do_not_disturb = 1;
            $customer->save();
          }
        }

        // Auto Replies
        $auto_replies = AutoReply::all();

        foreach ($auto_replies as $auto_reply) {
          if (array_key_exists('message', $params) && $params['message'] != '') {
            $keyword = $auto_reply->keyword;

            if (preg_match("/{$keyword}/i", $params['message'])) {
              $temp_params = $params;
              $temp_params['message'] = $auto_reply->reply;
              $temp_params['status'] = 1;

              ChatMessage::create($temp_params);
            }
          }
        }
      }

      if (!isset($user) && !isset($purchase) && !isset($customer)) {
        $modal_type = 'leads';
        // $new_name = "whatsapp lead " . uniqid( TRUE );
        $user = User::get()[0];
        $validate_phone['phone'] = $from;

        $validator = Validator::make($validate_phone, [
    			'phone' => 'unique:customers,phone'
    		]);

    		if ($validator->fails()) {

    		} else {
          $customer = new Customer;
          $customer->name = $from;
          $customer->phone = $from;
          $customer->rating = 2;
          $customer->save();

          $lead = Leads::create([
            'customer_id' => $customer->id,
            'client_name' => $from,
            'contactno' => $from,
            'rating' => 2,
            'status' => 1,
            'assigned_user' => $user->id,
            'userid' => $user->id,
            'whatsapp_number' => $to
          ]);

          $params['lead_id'] = $lead->id;
          $params['customer_id'] = $customer->id;
          $params = $this->modifyParamsWithMessage($params, $data);
          $message = ChatMessage::create($params);
          $model_type = 'leads';
          $model_id = $lead->id;
        }
      }

      // Auto Respond
      $today_date = Carbon::now()->format('Y-m-d');
      $chat_messages_count = ChatMessage::where('customer_id', $params['customer_id'])->where('created_at', 'LIKE', "%$today_date%")->whereNotNull('number')->count();
      $chat_messages_evening_count = ChatMessage::where('customer_id', $params['customer_id'])->where('created_at', '>', "$today_date 17:30")->whereNotNull('number')->count();

      if ($chat_messages_count == 1) {
        $time = Carbon::now();
        $morning = Carbon::create($time->year, $time->month, $time->day, 10, 0, 0);
        $evening = Carbon::create($time->year, $time->month, $time->day, 17, 30, 0);
        $saturday = Carbon::now()->endOfWeek()->subDay();
        $sunday = Carbon::now()->endOfWeek();

        $customer = Customer::find($params['customer_id']);
        $params = [
           'number'       => NULL,
           'user_id'      => 6,
           'approved'     => 1,
           'status'       => 9,
           'customer_id'  => $params['customer_id']
         ];

         if (!$time->between($morning, $evening, true) || $time == $saturday || $time == $sunday) {
           // $params['message'] = 'Our office is closed due to Good Friday we shall revert on all messages tomorrow.';
           $params['message'] = 'Our office is currently closed - we work between 10 - 5.30 - Monday - Friday -  - if an associate is available - your messaged will be responded within 60 minutes or on the next working day -since the phone is connected to a server it shows online - messages read  24 / 7 - but the message is directed to the concerned associate shall respond accordingly.';
         } else {
           $params['message'] = 'Hello we have received your message - and the concerned asscociate will revert asap - since the phone is connected to a server it shows online - messages read  24 / 7 - but the message is directed to the concerned associate and response us time is 60 minutes  .Pls. note that we do not answer calls on this number as its linked to our servers.';
         }

        $additional_message = ChatMessage::create($params);

        $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, FALSE, $additional_message->id);
      }

      if ($chat_messages_evening_count == 1) {
        $customer = Customer::find($params['customer_id']);
        $params = [
           'number'       => NULL,
           'user_id'      => 6,
           'approved'     => 1,
           'status'       => 9,
           'customer_id'  => $params['customer_id'],
           'message'      => 'Our office is currently closed - we work between 10 - 5.30 - Monday - Friday -  - if an associate is available - your messaged will be responded within 60 minutes or on the next working day -since the phone is connected to a server it shows online - messages read  24 / 7 - but the message is directed to the concerned associate shall respond accordingly.'
         ];

         $additional_message = ChatMessage::create($params);

         $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, FALSE, $additional_message->id);
      }
    } else {
      $custom_data = json_decode($data['custom_data'], true);

      $chat_message = ChatMessage::find($custom_data['chat_message_id']);

      if ($chat_message) {
        $chat_message->sent = 1;
        $chat_message->save();
      }
    }

    return response("");
    }
    public function getAllMessages(Request $request) {
        // $cid = $request->get('customerId');
        // $sql = "SELECT
        //     all_messages.*
        // FROM
        //     (
        //     SELECT
        //         messages.`id`,
        //         null AS `number`
        //         `customer_id`,
        //         `userid` AS `user_id`,
        //         `status`,
        //         `body` AS `message`,
        //         null AS `approved`,
        //         null AS `sent`,
        //         `created_at`
        //         'messages' AS `table_name`,
        //         (
        //         SELECT
        //             GROUP_CONCAT(
        //                 DISTINCT mediables.media_id SEPARATOR ','
        //             )
        //         FROM
        //             mediables
        //         WHERE
        //             mediables.mediable_id = messages.id
        //     ) AS media_ids,
        //     messages.`created_at`
        // FROM
        //     `messages`
        // WHERE
        //     $cid = `messages`.`customer_id`
        // UNION
        // SELECT
        //     chat_messages.`id`,
        //     `number`,
        //     `customer_id`,
        //     `user_id`,
        //     `status`,
        //     `message`,
        //     `approved`,
        //     `sent`,
        //     `created_at`,
        //     'chat_messages' AS `table_name`,
        //     (
        //     SELECT
        //         GROUP_CONCAT(
        //             DISTINCT mediables.media_id SEPARATOR ','
        //         )
        //     FROM
        //         mediables
        //     WHERE
        //         mediables.mediable_id = chat_messages.id
        // ) AS media_ids,
        // chat_messages.`created_at`
        // FROM
        //     `chat_messages`
        // WHERE
        //     $cid = `chat_messages`.`customer_id`
        // ) `all_messages`
        // ORDER BY
        //     all_messages.`created_at`";


            $chat_messages = DB::table('chat_messages')->where('customer_id', $request->customerId);

            $chat_messages = $chat_messages->join(DB::raw('(SELECT media_id, mediable_type, mediable_id FROM `mediables` WHERE mediable_type = "App\ChatMessage") as mediables LEFT JOIN (SELECT media_id FROM `mediables`) as product_mediables ON mediables.media_id = product_mediables.media_id'), 'chat_messages.id', '=', 'mediables.mediable_id', 'LEFT');

            $messages = DB::table('messages')->where('customer_id', $request->customerId);

            $messages = $messages->join(DB::raw('(SELECT media_id, mediable_type, mediable_id FROM `mediables` WHERE mediable_type = "App\Message") as mediables'), 'messages.id', '=', 'mediables.mediable_id', 'LEFT');


            $product_image = Product::with('Media')->whereHas('Media', function($q) {
               $q->where('media.id', '57075');
            })->select(['id', 'price_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->get();

            dD($product_image->toSql());

            foreach ($chat_messages->get() as $chat_message) {
              dump($chat_message->product_mediables);
            }
            dd('stap');





        $data = DB::select(DB::raw($sql));
        $messages = [];

        foreach ($data as $datum) {
          dd($datum);
            $images = Media::whereIn('id', explode(',', $datum->media_ids))->get(['disk', 'filename', 'extension'])->toArray();
            $images = array_map(function($item) {
                return URL::to('/') . '/' . $item['disk'] . '/' . $item['filename'] . '.' . $item['extension'];
            }, $images);
            $message = [
                'id' => $datum->id,
                'message' => $datum->message,
                'images' => $images
            ];
            $messages[] = $message;
        }

        return $messages;

    }


    /**
     * Send message
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request, $context)
    {
      $this->validate($request, [
        'message'         => 'nullable|required_without:image,screenshot_path|string',
        'image'           => 'nullable|required_without:message',
        'screenshot_path' => 'nullable|required_without:message',
        'customer_id'     => 'sometimes|nullable|numeric',
        'status'          => 'required|numeric',
        'assigned_to'     => 'sometimes|nullable',
      ]);

      $data = $request->except( '_token');
      $data['user_id'] = Auth::id();
      $data['number'] = NULL;
      $params['status'] = 1;

      if ($context == 'customer') {
        $data['customer_id'] = $request->customer_id;
        $module_id = $request->customer_id;
      } elseif ($context == "purchase") {
        $data['purchase_id'] = $request->purchase_id;
        $module_id = $request->purchase_id;
      }

      $chat_message = ChatMessage::create($data);

      if ($request->hasFile('image')) {
        $media = MediaUploader::fromSource($request->file('image'))->upload();
        $chat_message->attachMedia($media,config('constants.media_tags'));
      }

      if ($request->images) {
        foreach (json_decode($request->images) as $image) {
          $media = Media::find($image);
          $chat_message->attachMedia($media,config('constants.media_tags'));
        }
      }

      if ($request->screenshot_path != '') {
        $image_path = public_path() . '/uploads/temp_screenshot.png';
        $img = substr($request->screenshot_path, strpos($request->screenshot_path, ",")+1);
        $img = Image::make(base64_decode($img))->encode('png')->save($image_path);

        $media = MediaUploader::fromSource($image_path)->upload();
        $chat_message->attachMedia($media,config('constants.media_tags'));

        File::delete('uploads/temp_screenshot.png');
      }

      if ($request->ajax()) {
        return response()->json(['message' => $chat_message]);
      }

      return redirect('/'. $context .'/'.$module_id);
    }

    public function sendMultipleMessages(Request $request)
    {
      $selected_leads = json_decode($request->selected_leads, true);
      $leads = Leads::whereIn('id', $selected_leads)->whereNotNull('contactno')->get();

      if (count($leads) > 0) {
        foreach ($leads as $lead) {
          try {
            $params = [];
            $model_type = 'leads';
            $model_id = $lead->id;
            $params = [
              'lead_id' => $lead->id,
              'number'  => NULL,
              'message' => $request->message,
              'user_id' => Auth::id()
            ];

            if ($lead->customer) {
              $params['customer_id'] = $lead->customer->id;
            }

            $message = ChatMessage::create($params);

            // NotificationQueueController::createNewNotification([
            //   'message' => 'WAA - ' . $message->message,
            //   'timestamps' => ['+0 minutes'],
            //   'model_type' => $model_type,
            //   'model_id' =>  $model_id,
            //   'user_id' => Auth::id(),
            //   'sent_to' => '',
            //   'role' => 'message',
            // ]);
            //
            // NotificationQueueController::createNewNotification([
            //   'message' => 'WAA - ' . $message->message,
            //   'timestamps' => ['+0 minutes'],
            //  'model_type' => $model_type,
            //   'model_id' =>  $model_id,
            //   'user_id' => Auth::id(),
            //   'sent_to' => '',
            //   'role' => 'Admin',
            // ]);
           } catch (\Exception $ex) {
               return response($ex->getMessage(), 500);
           }
        }
      }

       return redirect()->route('leads.index');
    }

    public function updateAndCreate(Request $request)
    {
      $result = 'success';
      $message = Message::find($request->message_id);
      $params = [
        'number'  => NULL,
        'status'  => 1,
        'user_id' => Auth::id(),
      ];

      if ($message) {
        $params = [
          'approved' => 1,
          'status'   => 2,
          'created_at'  => Carbon::now()
        ];

        if ($request->moduletype == 'leads') {
          $params['lead_id'] = $message->moduleid;
          if ($lead = Leads::find($message->moduleid)) {
            if ($lead->customer) {
              $params['customer_id'] = $lead->customer->id;
            }
          }
        } elseif ($request->moduletype == 'orders') {
          $params['order_id'] = $message->moduleid;
          if ($order = Order::find($message->moduleid)) {
            if ($order->customer) {
              $params['customer_id'] = $order->customer->id;
            }
          }
        } elseif ($request->moduletype == 'customer') {
          $customer = Customer::find($message->customer_id);
          $params['customer_id'] = $customer->id;
        } elseif ($request->moduletype == 'purchase') {
          $params['purchase_id'] = $message->moduleid;
        }

        $images = $message->getMedia(config('constants.media_tags'));

        if ($images->first()) {
          $params['message'] = NULL;
          $chat_message = ChatMessage::create($params);

          foreach ($images as $img) {
            $chat_message->attachMedia($img,config('constants.media_tags'));
          }
        } else {
          $params['message'] = $message->body;

          $chat_message = ChatMessage::create($params);
        }

        $myRequest = new Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        $result = $this->approveMessage($request->moduletype, $myRequest);

      } else {
        if ($request->moduletype == 'customer') {
          $params['customer_id'] = $request->moduleid;
          $params['order_id'] = NULL;
        }
        elseif ($request->moduletype == 'leads') {
          $params['lead_id'] = $request->moduleid;
          if ($lead = Leads::find($request->moduleid)) {
            if ($lead->customer) {
              $params['customer_id'] = $lead->customer->id;
            }
          }
        } else {
          $params['order_id'] = $request->moduleid;
          if ($order = Order::find($request->moduleid)) {
            if ($order->customer) {
              $params['customer_id'] = $order->customer->id;
            }
          }
        }

        if ($request->images) {
          $params['message'] = NULL;
          $chat_message = ChatMessage::create($params);
          foreach (json_decode($request->images) as $image) {
            // $product = Product::find($product_id);
            // $media = $product->getMedia(config('constants.media_tags'))->first();
            // $params['media_url'] = $media->getUrl();
            $media = Media::find($image);
            $chat_message->attachMedia($media,config('constants.media_tags'));
          }
        }

        return redirect('/'. $request->moduletype.'/'.$request->moduleid);
      }

      return response()->json(['status' => $result]);
    }

    public function forwardMessage(Request $request)
    {
      $message = ChatMessage::find($request->message_id);

      foreach ($request->customer_id as $customer_id) {
        $new_message = new ChatMessage;
        $new_message->number = $message->number;
        $new_message->message = $message->message;
        $new_message->lead_id = $message->lead_id;
        $new_message->order_id = $message->order_id;
        $new_message->user_id = $message->user_id;
        $new_message->customer_id = $customer_id;
        $new_message->status = 1;
        $new_message->media_url = $message->media_url;

        $new_message->save();

        if ($images = $message->getMedia(config('constants.media_tags'))) {
          foreach ($images as $image) {
            $new_message->attachMedia($image,config('constants.media_tags'));
          }
        }
      }

      return redirect()->back();
    }
	/**
     * poll messages
     *
     * @return \Illuminate\Http\Response
     */
    public function pollMessages(Request $request, $context)
    {
      $params = [];
      $result = [];
      $skip = $request->page && $request->page > 1 ? $request->page * 10 : 0;

      switch ($context) {
  			case 'customer':
            $column = 'customer_id';
            $column_value = $request->customerId;
            break;
  			case 'purchase':
            $column = 'purchase_id';
            $column_value = $request->purchaeId;
            break;
  			default :
            $column = 'customer_id';
            $column_value = $request->customerId;
  		}

      $messages = ChatMessage::select(['id', "$column", 'number', 'user_id', 'assigned_to', 'approved', 'status', 'sent', 'created_at', 'media_url', 'message'])->where($column, $column_value)->latest();
      // ->join(DB::raw('(SELECT mediables.media_id, mediables.mediable_type, mediables.mediable_id FROM `mediables`) as mediables'), 'chat_messages.id', '=', 'mediables.mediable_id', 'RIGHT')
      // ->selectRaw('id, customer_id, number, user_id, assigned_to, approved, status, sent, created_at, media_url, message, mediables.media_id, mediables.mediable_id')->where('customer_id', $request->customerId)->latest();


      // foreach ($messages->get() as $message) {
      //   foreach ($message->media_id as $med) {
      //     dump($med);
      //   }
      // }

      // dd('stap');

      // IS IT NECESSARY ?
      if ($request->get("elapse")) {
        $elapse = (int) $request->get("elapse");
        $date = new \DateTime;
        $date->modify(sprintf("-%s seconds", $elapse));
        // $messages = $messages->where('created_at', '>=', $date->format('Y-m-d H:i:s'));
      }

      foreach ($messages->get() as $message) {
        $messageParams = [
          'id' => $message->id,
          'number' => $message->number,
          'assigned_to' => $message->assigned_to,
          'created_at' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
          'approved' => $message->approved,
          'status'  => $message->status,
          'user_id' => $message->user_id,
          'sent'    => $message->sent
        ];

        if ($message->media_url) {
          $messageParams['media_url'] = $message->media_url;
          $headers = get_headers($message->media_url, 1);
          $messageParams['content_type'] = $headers["Content-Type"][1];
        }

        if ($message->message) {
          $messageParams['message'] = $message->message;
        }

        if ($message->hasMedia(config('constants.media_tags'))) {
          $images_array = [];

          foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
            $temp_image = [
              'key'          => $image->getKey(),
              'image'        => $image->getUrl(),
              'product_id'   => '',
              'special_price'=> '',
              'size'         => ''
            ];

            $image_key = $image->getKey();

            $product_image = Product::with('Media')
            ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key)")
            // ->whereHas('Media', function($q) use($image) {
            //    $q->where('media.id', $image->getKey());
            // })
            ->select(['id', 'price_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

            if ($product_image) {
              $temp_image['product_id'] = $product_image->id;
              $temp_image['special_price'] = $product_image->price_special;

              $string = $product_image->supplier;
              $expr = '/(?<=\s|^)[a-z]/i';
              preg_match_all($expr, $string, $matches);
              $supplier_initials = implode('', $matches[0]);
              $temp_image['supplier_initials'] = strtoupper($supplier_initials);

              if ($product_image->size != NULL) {
                $temp_image['size'] = $product_image->size;
              } else {
                $temp_image['size'] = (string) $product_image->lmeasurement . ', ' . (string) $product_image->hmeasurement . ', ' . (string) $product_image->dmeasurement;
              }
            }

            array_push($images_array, $temp_image);
          }

          $messageParams['images'] = $images_array;
        }

        $result[] = array_merge($params, $messageParams);
      }

     // $messages = Message::where('moduleid','=', $id)->where('moduletype','=', $model_type)->orderBy("created_at", 'desc')->get();
     // foreach ($messages->toArray() as $key => $message) {
     //   $images_array = [];
     //   if ($images = $messages[$key]->getMedia(config('constants.media_tags'))) {
     //     foreach ($images as $image) {
     //       $temp_image = [
     //         'key'          => $image->getKey(),
     //         'image'        => $image->getUrl(),
     //         'product_id'   => '',
     //         'special_price'=> '',
     //         'size'         => ''
     //       ];
     //
     //       $product_image = Product::with('Media')->whereHas('Media', function($q) use($image) {
     //                           $q->where('media.id', $image->getKey());
     //                         })->first();
     //       if ($product_image) {
     //         $temp_image['product_id'] = $product_image->id;
     //         $temp_image['special_price'] = $product_image->price_special;
     //
     //         if ($product_image->size != NULL) {
     //           $temp_image['size'] = $product_image->size;
     //         } else {
     //           $temp_image['size'] = (string) $product_image->lmeasurement . ', ' . (string) $product_image->hmeasurement . ', ' . (string) $product_image->dmeasurement;
     //         }
     //       }
     //
     //       array_push($images_array, $temp_image);
     //     }
     //   }
     //
     //   $message['images'] = $images_array;
     //   array_push($result, $message);
     // }

     $result = array_values(collect($result)->sortBy('created_at')->reverse()->toArray());
     $currentPage = LengthAwarePaginator::resolveCurrentPage();
     $perPage = 10;

     if ($request->page) {
       $currentItems = array_slice($result, $perPage * ($currentPage - 1), $perPage);
     } else {
       $currentItems = array_reverse(array_slice($result, $perPage * ($currentPage - 1), $perPage));
     }

     $result = new LengthAwarePaginator($currentItems, count($result), $perPage, $currentPage, [
       'path'	=> LengthAwarePaginator::resolveCurrentPath()
     ]);
       return response()->json( $result );
    }

    public function pollMessagesCustomer(Request $request)
    {
      $params = [];
      $result = [];
      $skip = $request->page && $request->page > 1 ? $request->page * 10 : 0;

      // $messages = ChatMessage::select(['id', 'customer_id', 'number', 'user_id', 'assigned_to', 'approved', 'status', 'sent', 'created_at', 'media_url', 'message'])->where('customer_id', $request->customerId)->latest();

      $messages = ChatMessage::select(['id', 'customer_id', 'number', 'user_id', 'assigned_to', 'approved', 'status', 'sent', 'created_at', 'media_url', 'message'])->where('customer_id', $request->customerId)->latest();
      // ->join(DB::raw('(SELECT mediables.media_id, mediables.mediable_type, mediables.mediable_id FROM `mediables`) as mediables'), 'chat_messages.id', '=', 'mediables.mediable_id', 'RIGHT')
      // ->selectRaw('id, customer_id, number, user_id, assigned_to, approved, status, sent, created_at, media_url, message, mediables.media_id, mediables.mediable_id')->where('customer_id', $request->customerId)->latest();


      // foreach ($messages->get() as $message) {
      //   foreach ($message->media_id as $med) {
      //     dump($med);
      //   }
      // }

      // dd('stap');

      // IS IT NECESSARY ?
      if ($request->get("elapse")) {
        $elapse = (int) $request->get("elapse");
        $date = new \DateTime;
        $date->modify(sprintf("-%s seconds", $elapse));
        // $messages = $messages->where('created_at', '>=', $date->format('Y-m-d H:i:s'));
      }

      foreach ($messages->get() as $message) {
        $messageParams = [
          'id' => $message->id,
          'number' => $message->number,
          'assigned_to' => $message->assigned_to,
          'created_at' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
          'approved' => $message->approved,
          'status'  => $message->status,
          'user_id' => $message->user_id,
          'sent'    => $message->sent
        ];

        if ($message->media_url) {
          $messageParams['media_url'] = $message->media_url;
          $headers = get_headers($message->media_url, 1);
          $messageParams['content_type'] = $headers["Content-Type"][1];
        }

        if ($message->message) {
          $messageParams['message'] = $message->message;
        }

        if ($message->hasMedia(config('constants.media_tags'))) {
          $images_array = [];

          foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
            $temp_image = [
              'key'          => $image->getKey(),
              'image'        => $image->getUrl(),
              'product_id'   => '',
              'special_price'=> '',
              'size'         => ''
            ];

            $image_key = $image->getKey();
            $mediable_type = "Product";

            $product_image = Product::with('Media')
            ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
            // ->whereHas('Media', function($q) use($image) {
            //    $q->where('media.id', $image->getKey());
            // })
            ->select(['id', 'price_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

            // dd($product_image);

            if ($product_image) {
              $temp_image['product_id'] = $product_image->id;
              $temp_image['special_price'] = $product_image->price_special;

              $string = $product_image->supplier;
              $expr = '/(?<=\s|^)[a-z]/i';
              preg_match_all($expr, $string, $matches);
              $supplier_initials = implode('', $matches[0]);
              $temp_image['supplier_initials'] = strtoupper($supplier_initials);

              if ($product_image->size != NULL) {
                $temp_image['size'] = $product_image->size;
              } else {
                $temp_image['size'] = (string) $product_image->lmeasurement . ', ' . (string) $product_image->hmeasurement . ', ' . (string) $product_image->dmeasurement;
              }
            }

            array_push($images_array, $temp_image);
          }

          $messageParams['images'] = $images_array;
        }

        $result[] = array_merge($params, $messageParams);
      }

      // $messages = Message::select(['id', 'customer_id', 'userid', 'status', 'assigned_to', 'body', 'created_at'])->where('customer_id', $request->customerId)->latest()->get();
      //
      // foreach ($messages->toArray() as $key => $message) {
      //   $images_array = [];
      //
      //   if ($images = $messages[$key]->getMedia(config('constants.media_tags'))) {
      //     foreach ($images as $image) {
      //       $temp_image = [
      //       'key'          => $image->getKey(),
      //       'image'        => $image->getUrl(),
      //       'product_id'   => '',
      //       'special_price'=> '',
      //       'size'         => ''
      //       ];
      //
      //       $product_image = Product::with('Media')->whereHas('Media', function($q) use($image) {
      //         $q->where('media.id', $image->getKey());
      //       })->select(['id', 'price_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();
      //
      //       if ($product_image) {
      //         $temp_image['product_id'] = $product_image->id;
      //         $temp_image['special_price'] = $product_image->price_special;
      //
      //         $string = $product_image->supplier;
      //         $expr = '/(?<=\s|^)[a-z]/i';
      //         preg_match_all($expr, $string, $matches);
      //         $supplier_initials = implode('', $matches[0]);
      //         $temp_image['supplier_initials'] = strtoupper($supplier_initials);
      //
      //         if ($product_image->size != NULL) {
      //           $temp_image['size'] = $product_image->size;
      //         } else {
      //           $temp_image['size'] = (string) $product_image->lmeasurement . ', ' . (string) $product_image->hmeasurement . ', ' . (string) $product_image->dmeasurement;
      //         }
      //       }
      //
      //       array_push($images_array, $temp_image);
      //     }
      //   }
      //
      //   $message['images'] = $images_array;
      //   array_push($result, $message);
      // }
      // $messages = $messages->paginate(24);
      // dd('stap');

      $result = array_values(collect($result)->sortBy('created_at')->reverse()->toArray());
      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $perPage = 10;

      if ($request->page) {
        $currentItems = array_slice($result, $perPage * ($currentPage - 1), $perPage);

      } else {
        $currentItems = array_reverse(array_slice($result, $perPage * ($currentPage - 1), $perPage));
        $result = array_reverse($result);
      }

      $result = new LengthAwarePaginator($currentItems, count($result), $perPage, $currentPage, [
        'path'	=> LengthAwarePaginator::resolveCurrentPath()
      ]);

      return response()->json($result);
    }

    public function approveMessage($context, Request $request)
	{
        $user = \Auth::user();
        $message = ChatMessage::findOrFail($request->get("messageId"));
        $today_date = Carbon::now()->format('Y-m-d');

        if ($context == "customer") {
          $chat_messages_count = ChatMessage::where('customer_id', $message->customer_id)->where('created_at', 'LIKE', "%$today_date%")->whereNull('number')->count();

          if ($chat_messages_count == 1) {
            $customer = Customer::find($message->customer_id);
            $params = [
               'number'       => NULL,
               'user_id'      => Auth::id(),
               'approved'     => 1,
               'status'       => 2,
               'customer_id'  => $message->customer_id,
               'message'      => 'This Number is just for whats app messages and NOT CALLS , for calls pls. use our toll free number 0008000401700 - ( care to be taken not to dial with 91 or  + 91 ). Pls. leave a message if you cannot connect to our toll free number and we will call you back at the earliest.'
             ];

            $additional_message = ChatMessage::create($params);

            $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, TRUE, $additional_message->id);

            sleep(5);
          }

          if (Setting::get('whatsapp_number_change') == 1) {
            $customer = Customer::find($message->customer_id);
            $default_api = ApiKey::where('default', 1)->first();

            if (!$customer->whatsapp_number_change_notified() && $default_api->number != $customer->whatsapp_number) {
              $params = [
                 'number'       => NULL,
                 'user_id'      => Auth::id(),
                 'approved'     => 1,
                 'status'       => 2,
                 'customer_id'  => $message->customer_id,
                 'message'      => 'Our whatsapp number has changed'
               ];

              $additional_message = ChatMessage::create($params);

              $this->sendWithWhatsApp($customer->phone, $default_api->number, $additional_message->message, TRUE, $additional_message->id);

              sleep(5);

              CommunicationHistory::create([
        				'model_id'		=> $customer->id,
        				'model_type'	=> Customer::class,
        				'type'				=> 'number-change',
        				'method'			=> 'whatsapp'
        			]);
            }
          }
        }

        if ($message->message != '') {
          $customer = Customer::find($message->customer_id);
          $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $message->message, TRUE, $message->id);
        }

        if ($message->media_url != '') {
          $customer = Customer::find($message->customer_id);
          $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $message->media_url, TRUE, $message->id);
        }

        if ($images = $message->getMedia(config('constants.media_tags'))) {
          foreach ($images as $image) {
            $send = str_replace(' ', '%20', $image->getUrl());

            if ($context == "customer") {
              $customer = Customer::find($message->customer_id);
              $this->sendWithWhatsApp( $message->customer->phone,$customer->whatsapp_number, $send, TRUE, $message->id);
            } elseif ($context == 'purchase') {
              $purchase = Purchase::find($message->purchase_id);

              if ($purchase->agent) {
                $this->sendWithWhatsApp($purchase->agent->phone,$purchase->agent->whatsapp_number, $send, FALSE, $message->id);
              } else {
                return 'error';
              }
            }
          }
        }

        // $send = $message->message;
        // if (is_null($send)) {
        //   $send = $message->media_url;
        //
        //   if (is_null($send)) {
        //     if ($images = $message->getMedia(config('constants.media_tags'))) {
        //       foreach ($images as $image) {
        //         $send = str_replace(' ', '%20', $image->getUrl());
        //
        //         if ($context == "leads") {
        //           $lead = Leads::find($message->lead_id);
        //           $this->sendWithWhatsApp( $lead->contactno, $lead->whatsapp_number, $send, TRUE, $message->id);
        //         } elseif ( $context == "orders") {
        //           $order = Order::find($message->order_id);
        //           $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send, TRUE, $message->id);
        //         } elseif ($context == "customer") {
        //           $customer = Customer::find($message->customer_id);
        //           $this->sendWithWhatsApp( $message->customer->phone,$customer->whatsapp_number, $send, TRUE, $message->id);
        //         } elseif ($context == 'purchase') {
        //           $purchase = Purchase::find($message->purchase_id);
        //
        //           if ($purchase->agent) {
        //             $this->sendWithWhatsApp($purchase->agent->phone,$purchase->agent->whatsapp_number, $send, FALSE, $message->id);
        //           } else {
        //             return 'error';
        //           }
        //         }
        //       }
        //     }
        //   } else {
        //     if ($context == "leads") {
        //       $lead = Leads::find($message->lead_id);
        //       $this->sendWithWhatsApp( $lead->contactno, $lead->whatsapp_number, $send, TRUE, $message->id);
        //     } elseif ( $context == "orders") {
        //       $order = Order::find($message->order_id);
        //       $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send, TRUE, $message->id);
        //     } elseif ($context == "customer") {
        //       $customer = Customer::find($message->customer_id);
        //       $this->sendWithWhatsApp( $message->customer->phone,$customer->whatsapp_number, $send, TRUE, $message->id);
        //     } elseif($context == 'purchase') {
        //       $purchase = Purchase::find($message->purchase_id);
        //
        //       if ($purchase->agent) {
        //         $this->sendWithWhatsApp($purchase->agent->phone,$purchase->agent->whatsapp_number, $send, FALSE, $message->id);
        //       } else {
        //         return 'error';
        //       }
        //     }
        //   }
        // } else {
        //   if ($context == "leads") {
        //     $lead = Leads::find($message->lead_id);
        //     $this->sendWithWhatsApp($lead->contactno, $lead->whatsapp_number, $send, TRUE, $message->id);
        //   } elseif ( $context == "orders") {
        //     $order = Order::find($message->order_id);
        //     $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send, TRUE, $message->id);
        //   } elseif ($context == "customer") {
        //     $customer = Customer::find($message->customer_id);
        //     $this->sendWithWhatsApp($message->customer->phone,$customer->whatsapp_number, $send, TRUE, $message->id);
        //   } elseif ($context == 'purchase') {
        //     $purchase = Purchase::find($message->purchase_id);
        //
        //     if ($purchase->agent) {
        //       $this->sendWithWhatsApp($purchase->agent->phone,$purchase->agent->whatsapp_number, $send, FALSE, $message->id);
        //     } else {
        //       return 'error';
        //     }
        //   }
        // }

        $message->update([
          'approved' => 1,
          'status'   => 2,
          'created_at'  => Carbon::now()
        ]);

        return response("success");
    }

  public function sendToAll(Request $request, $validate = true)
  {
    if ($validate) {
      $this->validate($request, [
        'message'         => 'required_without:images',
        // 'images'          => 'required_without:message|mimetypes:image/jpeg,image/png',
        // 'images.*'        => 'required_without:message|mimetypes:image/jpeg,image/png',
        'file'            => 'sometimes|mimes:xlsx,xls',
        'sending_time'    => 'required|date',
        'whatsapp_number' => 'required_with:file',
        'frequency'       => 'required|numeric',
        'rating'          => 'sometimes|nullable|numeric'
      ]);
    }

    $frequency = $request->frequency;

    if ($request->moduletype == 'customers') {
      $content['message'] = $request->body;

      foreach (json_decode($request->images) as $key => $image) {
        $media = Media::find($image);

        $content['image'][$key]['key'] = $media->getKey();
        $content['image'][$key]['url'] = $media->getUrl();
      }
    } else {
      $content['message'] = $request->message;

      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $key => $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $content['image'][$key]['key'] = $media->getKey();
          $content['image'][$key]['url'] = $media->getUrl();
        }
      }
    }

    if ($request->linked_images != '') {
      $content['linked_images'] = json_decode($request->linked_images);
    }

    if ($request->to_all || $request->moduletype == 'customers') {
      $minutes = round(60 / $frequency);
      $max_group_id = MessageQueue::max('group_id') + 1;


      $data = Customer::whereNotNull('phone')->where('do_not_disturb', 0);

      if ($request->rating != '') {
        $data = $data->where('rating', $request->rating);
      }

      $data = $data->get()->groupBy('whatsapp_number');

      foreach ($data as $whatsapp_number => $customers) {
        $now = $request->sending_time ? Carbon::parse($request->sending_time) : Carbon::now();
        $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
        $evening = Carbon::create($now->year, $now->month, $now->day, 22, 0, 0);

        if ($whatsapp_number == '919152731486') {
          foreach ($customers as $customer) {
            if (!$now->between($morning, $evening, true)) {
              if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                // add day
                $now->addDay();
                $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $evening = Carbon::create($now->year, $now->month, $now->day, 22, 0, 0);
              } else {
                // dont add day
                $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $evening = Carbon::create($now->year, $now->month, $now->day, 22, 0, 0);
              }
            }

            MessageQueue::create([
              'user_id'       => Auth::id(),
              'customer_id'   => $customer->id,
              'phone'         => NULL,
              'type'          => 'message_all',
              'data'          => json_encode($content),
              'sending_time'  => $now,
              'group_id'      => $max_group_id
            ]);

            $now->addMinutes($minutes);
          }
        }

        if ($whatsapp_number == '919152731483') {
          foreach ($customers as $customer) {
            if (!$now->between($morning, $evening, true)) {
              if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                // add day

                $now->addDay();
                $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $evening = Carbon::create($now->year, $now->month, $now->day, 22, 0, 0);
              } else {
                // dont add day

                $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $evening = Carbon::create($now->year, $now->month, $now->day, 22, 0, 0);
              }
            }

            MessageQueue::create([
              'user_id'       => Auth::id(),
              'customer_id'   => $customer->id,
              'phone'         => NULL,
              'type'          => 'message_all',
              'data'          => json_encode($content),
              'sending_time'  => $now,
              'group_id'      => $max_group_id
            ]);

            $now->addMinutes($minutes);
          }
        }
      }
    } else {
      $minutes = round(60 / $frequency);
      $now = $request->sending_time ? Carbon::parse($request->sending_time) : Carbon::now();
      $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
      $evening = Carbon::create($now->year, $now->month, $now->day, 22, 0, 0);
      $max_group_id = MessageQueue::max('group_id') + 1;
      $array = Excel::toArray(new CustomerNumberImport, $request->file('file'));

      foreach ($array as $item) {
        foreach ($item as $it) {
          $number = (int)$it[0];

          if (!$now->between($morning, $evening, true)) {
            if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
              // add day
              $now->addDay();
              $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
              $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
              $evening = Carbon::create($now->year, $now->month, $now->day, 22, 0, 0);
            } else {
              // dont add day
              $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
              $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
              $evening = Carbon::create($now->year, $now->month, $now->day, 22, 0, 0);
            }
          }

          MessageQueue::create([
            'user_id'         => Auth::id(),
            'customer_id'     => NULL,
            'phone'           => $number,
            'whatsapp_number' => $request->whatsapp_number,
            'type'            => 'message_selected',
            'data'            => json_encode($content),
            'sending_time'    => $now,
            'group_id'        => $max_group_id
          ]);

          $now->addMinutes($minutes);
        }
      }
    }

    return redirect()->route('broadcast.images')->with('success', 'Messages are being sent in the background!');
  }

  public function stopAll() {
    $message_queues = MessageQueue::where('sent', 0)->where('status', 0)->get();

    foreach ($message_queues as $message_queue) {
      $message_queue->status = 1;
      $message_queue->save();
    }

    return redirect()->back()->with('success', 'Messages stopped processing!');
  }

	public function sendWithWhatsApp($number, $sendNumber, $text, $validation = true, $chat_message_id = null)
	{
    if ($validation == true) {
      if (Auth::id() != 3) {
        if (strlen($number) != 12 || !preg_match('/^[91]{2}/', $number)) {
          throw new \Exception("Invalid number format. Must be 12 digits and start with 91");
        }
      }
    }

    // foreach (\Config::get("apiwha.api_keys") as $config_key) {
    //   if ($config_key['number'] == $number) {
    //     return;
    //   }
    // }

    $api_keys = ApiKey::all();

    foreach ($api_keys as $api_key) {
      if ($api_key->number == $number) {
        return;
      }
    }

    $curl = curl_init();

    if (Setting::get('whatsapp_number_change') == 1) {
      $keys = \Config::get("apiwha.api_keys");
      $key = $keys[0]['key'];

      foreach ($api_keys as $api_key) {
        if ($api_key->default == 1) {
          $key = $api_key->key;
        }
      }
    } else {
      if (is_null($sendNumber)) {
        $keys = \Config::get("apiwha.api_keys");
        $key = $keys[0]['key'];

        foreach ($api_keys as $api_key) {
          if ($api_key->default == 1) {
            $key = $api_key->key;
          }
        }
      } else {
        // $config = $this->getWhatsAppNumberConfig($sendNumber);
        // $key = $config['key'];

        $keys = \Config::get("apiwha.api_keys");
        $key = $keys[0]['key'];

        foreach ($api_keys as $api_key) {
          if ($api_key->default == 1) {
            $key = $api_key->key;
          }
        }

        foreach ($api_keys as $api_key) {
          if ($api_key->number == $sendNumber) {
            $key = $api_key->key;
          }
        }
      }
    }

    $encodedNumber = urlencode($number);
    $encodedText = urlencode($text);

    if ($chat_message_id) {
      $custom_data = [
        'chat_message_id' => $chat_message_id
      ];

      $encodedCustomData = urlencode(json_encode($custom_data));
    } else {
      $encodedCustomData = '';
    }
    //$number = "";
    $url = "https://panel.apiwha.com/send_message.php?apikey=".$key."&number=".$encodedNumber."&text=" . $encodedText . "&custom_data=" . $encodedCustomData;
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      throw new \Exception("cURL Error #:" . $err);
    } else {
      $result = json_decode( $response );
      if (!$result->success) {
        throw new \Exception("whatsapp request error: " . $result->description);
       }
    }
	}
    private function getWhatsAppNumberConfig($target)
    {
        $numbers = \Config::get("apiwha.api_keys");
        foreach ($numbers as $number) {
            if ($number['number'] == $target) {
                return $number;
            }
        }

        return $numbers[0];
    }
    private function formatChatDate($date)
    {
        return $date->format("Y-m-d h:iA");
    }
    private function modifyParamsWithMessage($params, $data)
    {
        if (filter_var($data['text'], FILTER_VALIDATE_URL)) {
  // you're good
            $path = $data['text'];
            $paths = explode("/", $path);
            $file = $paths[ count( $paths ) - 1];
            $extension = explode(".", $file)[1];
            $fileName = uniqid(TRUE).".".$extension;
            $contents = file_get_contents($path);
            if ( file_put_contents(implode(DIRECTORY_SEPARATOR, array(\Config::get("apiwha.media_path"), $fileName)), $contents ) ==  FALSE) {
                return FALSE;
            }
            $url = implode("/", array( \Config::get("app.url"), "apiwha", "media", $fileName ));
            $params['media_url'] =$url;
            return $params;
        }
        $params['message']=$data['text'];
        return $params;
    }

    public function updatestatus(Request $request)
    {
      $message = ChatMessage::find($request->get('id'));
      $message->status = $request->get('status');
      $message->save();

      return response('success');
    }
}
