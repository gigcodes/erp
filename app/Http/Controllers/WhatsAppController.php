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
use App\BroadcastImage;
use App\Leads;
use App\Order;
use App\Task;
use App\Status;
use App\Supplier;
use App\Setting;
use App\Dubbizle;
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
      $supplier = $this->findSupplierByNumber($from);
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

        $params['erp_user'] = $user->id;

        $params = $this->modifyParamsWithMessage($params, $data);

        if (array_key_exists('message', $params) && (preg_match_all("/TASK ID ([\d]+)/i", $params['message'], $match))) {
          $params['task_id'] = $match[1][0];
        }

        $message = ChatMessage::create($params);
        $model_type = 'user';
        $model_id = $user->id;
      }

      if ($supplier) {
        // $params['lead_id'] = null;
        // $params['order_id'] = null;
        $params['erp_user'] = NULL;
        $params['task_id'] = NULL;
        $params['supplier_id'] = $supplier->id;

        $params = $this->modifyParamsWithMessage($params, $data);
        $message = ChatMessage::create($params);
        $model_type = 'supplier';
        $model_id = $supplier->id;
        // $supplier->update([
        //   'whatsapp_number' => $to
        // ]);
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
        $params['erp_user'] = NULL;
        $params['supplier_id'] = NULL;
        $params['task_id'] = NULL;
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

            $dnd_params = [
               'number'       => NULL,
               'user_id'      => 6,
               'approved'     => 1,
               'status'       => 9,
               'customer_id'  => $customer->id,
               'message'      => 'Greetings from Sololuxury.co.in, based on your request we have now placed you on DND. You will no longer receive updates from us. We apologize for the inconvenience caused.'
             ];

            $auto_dnd_message = ChatMessage::create($dnd_params);

            $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $dnd_params['message'], FALSE, $auto_dnd_message->id);
          }
        }

        // Auto Instruction
        if (array_key_exists('message', $params) && (preg_match("/price/i", $params['message']) || preg_match("/you photo/i", $params['message']) || preg_match("/pp/i", $params['message']) || preg_match("/how much/i", $params['message']) || preg_match("/cost/i", $params['message']) || preg_match("/rate/i", $params['message']))) {
          if ($customer = Customer::find($params['customer_id'])) {
            $two_hours = Carbon::now()->subHours(2);
            $latest_broadcast_message = ChatMessage::where('customer_id', $customer->id)->where('created_at', '>', $two_hours)->where('status', 8)->latest()->first();

            if ($latest_broadcast_message) {
              if (!$latest_broadcast_message->is_sent_broadcast_price()) {
                if ($latest_broadcast_message->hasMedia(config('constants.media_tags'))) {
                  $selected_products = [];

                  foreach ($latest_broadcast_message->getMedia(config('constants.media_tags')) as $image) {
                    $image_key = $image->getKey();
                    $mediable_type = "BroadcastImage";

                    $broadcast = BroadcastImage::with('Media')
                    ->whereRaw("broadcast_images.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
                    ->first();

                    if ($broadcast) {
                      $brod_products = json_decode($broadcast->products, true);

                      if (count($brod_products) > 0) {
                        foreach ($brod_products as $brod_pro) {
                          $selected_products[] = $brod_pro;
                        }
                      }
                    }
                  }

                  if (isset($broadcast)) {
                    $quick_lead = Leads::create([
                      'customer_id' => $customer->id,
                      'rating'  => 1,
                      'status'  => 3,
                      'assigned_user' => 6,
                      'selected_product'  => json_encode($selected_products),
                      'created_at'  => Carbon::now()
                    ]);

                    $requestData = new Request();
                    $requestData->setMethod('POST');
                    $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                    app('App\Http\Controllers\LeadsController')->sendPrices($requestData);

                    CommunicationHistory::create([
              				'model_id'		=> $latest_broadcast_message->id,
              				'model_type'	=> ChatMessage::class,
              				'type'				=> 'broadcast-prices',
              				'method'			=> 'whatsapp'
              			]);
                  } else {
                    // Instruction::create([
                    //   'customer_id' => $customer->id,
                    //   'instruction' => 'Please send the prices',
                    //   'category_id' => 1,
                    //   'assigned_to' => 7,
                    //   'assigned_from' => 6
                    // ]);
                  }
                }
              }
            }

            Instruction::create([
              'customer_id' => $customer->id,
              'instruction' => 'Please send the prices',
              'category_id' => 1,
              'assigned_to' => 7,
              'assigned_from' => 6
            ]);
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
      $time = Carbon::now();
      $morning = Carbon::create($time->year, $time->month, $time->day, 10, 0, 0);
      $not_morning = Carbon::create($time->year, $time->month, $time->day, 0, 0, 0);
      $evening = Carbon::create($time->year, $time->month, $time->day, 17, 30, 0);
      $not_evening = Carbon::create($time->year, $time->month, $time->day, 23, 59, 0);
      $saturday = Carbon::now()->endOfWeek()->subDay()->format('Y-m-d');
      $sunday = Carbon::now()->endOfWeek()->format('Y-m-d');

      $chat_messages_query = ChatMessage::where('customer_id', $params['customer_id'])->whereBetween('created_at', [$morning, $evening])->whereNotNull('number');
      $chat_messages_count = $chat_messages_query->count();

      $chat_messages_evening_query = ChatMessage::where('customer_id', $params['customer_id'])->where(function($query) use ($not_morning, $morning, $evening, $not_evening) {
        $query->whereBetween('created_at', [$not_morning, $morning])->orWhereBetween('created_at', [$evening, $not_evening]);
      })->whereNotNull('number');
      $chat_messages_evening_count = $chat_messages_evening_query->count();

      if ($chat_messages_count == 1) {
        $chat_messages_query_first = $chat_messages_query->first();
      }

      if ($chat_messages_evening_count == 1) {
        $chat_messages_evening_query_first = $chat_messages_evening_query->first();
      }

      if ($chat_messages_count == 1 && (isset($chat_messages_query_first) && $chat_messages_query_first->id == $message->id) && ($saturday != $today_date && $sunday != $today_date)) {
        $customer = Customer::find($params['customer_id']);
        $params = [
           'number'       => NULL,
           'user_id'      => 6,
           'approved'     => 1,
           'status'       => 9,
           'customer_id'  => $params['customer_id']
         ];

        if ($time->between($morning, $evening, true)) {
          $params['message'] = 'Hello we have received your message - and the concerned asscociate will revert asap - since the phone is connected to a server it shows online - messages read  24 / 7 - but the message is directed to the concerned associate and response us time is 60 minutes  .Pls. note that we do not answer calls on this number as its linked to our servers.';

          sleep(1);
          $additional_message = ChatMessage::create($params);
          $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, FALSE, $additional_message->id);
        }
      } else if (($chat_messages_evening_count == 1 && (isset($chat_messages_evening_query_first) && $chat_messages_evening_query_first->id == $message->id)) || ($chat_messages_count == 1 && ($saturday == $today_date || $sunday == $today_date))) {
        $customer = Customer::find($params['customer_id']);
        $params = [
           'number'       => NULL,
           'user_id'      => 6,
           'approved'     => 1,
           'status'       => 9,
           'customer_id'  => $params['customer_id'],
           'message'      => 'Our office is currently closed - we work between 10 - 5.30 - Monday - Friday -  - if an associate is available - your messaged will be responded within 60 minutes or on the next working day -since the phone is connected to a server it shows online - messages read  24 / 7 - but the message is directed to the concerned associate shall respond accordingly.'
         ];

         sleep(1);
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

    public function incomingMessageNew(Request $request)
    {
		$data = $request->json()->all();

    if ($data['event'] == 'message:in:new') {
      $to = str_replace('+', '', $data['data']['toNumber']);
  		$from = str_replace('+', '', $data['data']['fromNumber']);
  		$text = $data['data']['body'];
  		$lead = $this->findLeadByNumber($from);
      $user = $this->findUserByNumber($from);
      $supplier = $this->findSupplierByNumber($from);
      $customer = $this->findCustomerByNumber($from);
      $dubbizle = $this->findDubbizleByNumber($from);

      $params = [
        'number'  => $from,
        'message' => ''
      ];

      if ($data['data']['type'] == 'text') {
        $params['message'] = $text;
      } else if ($data['data']['type'] == 'image') {
        $image_data = $data['data']['media']['preview']['image'];
        $image_path = public_path() . '/uploads/temp_image.png';
        $img = Image::make(base64_decode($image_data))->encode('jpeg')->save($image_path);

        $media = MediaUploader::fromSource($image_path)->upload();

        File::delete('uploads/temp_image.png');
      }

      if ($user) {
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

        $params['erp_user'] = $user->id;

        if ($params['message'] != '' && (preg_match_all("/TASK ID ([\d]+)/i", $params['message'], $match))) {
          $params['task_id'] = $match[1][0];
        }

        // $params = $this->modifyParamsWithMessage($params, $data);
        $message = ChatMessage::create($params);
        $model_type = 'user';
        $model_id = $user->id;

        if ($user->id == 3) {
          file_put_contents(__DIR__."/response.txt", json_encode($data));

          if (array_key_exists('quoted', $data['data'])) {
            $quoted_id = $data['data']['quoted']['wid'];

            $configs = \Config::get("wassenger.api_keys");
            // $encodedNumber = "+" . $number;
            // $encodedText = $message;
            $wa_token = $configs[0]['key'];
            $wa_device = $configs[1]['device'];

            // $array = [
            //   'phone' => $encodedNumber,
            //   'message' => (string) $encodedText,
            //   'reference' => (string) $chat_message_id,
            //   'device'  => "$wa_device",
            //   'enqueue' => "$enqueue",
            // ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.wassenger.com/v1/io/$wa_device/messages/$quoted_id",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              // CURLOPT_POSTFIELDS => json_encode($array),
              CURLOPT_HTTPHEADER => array(
                // "content-type: application/json",
                "token: $wa_token"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            file_put_contents(__DIR__."/wow.txt", json_encode($response));

            if ($err) {
              throw new \Exception("cURL Error #:" . $err);
            } else {
              $result = json_decode($response, true);

              if ($http_code != 201) {
                throw new \Exception("Something was wrong with message: " . $result['message']);
              }
            }
          }
        }
      }

      if ($supplier) {
        $params['erp_user'] = NULL;
        $params['task_id'] = NULL;
        $params['supplier_id'] = $supplier->id;

        $message = ChatMessage::create($params);
        $model_type = 'supplier';
        $model_id = $supplier->id;
      }

      if ($dubbizle) {
        $params['erp_user'] = NULL;
        $params['task_id'] = NULL;
        $params['supplier_id'] = NULL;
        $params['dubbizle_id'] = $dubbizle->id;

        $message = ChatMessage::create($params);
        $model_type = 'dubbizle';
        $model_id = $dubbizle->id;
      }

      if ($customer) {
        $params['erp_user'] = NULL;
        $params['supplier_id'] = NULL;
        $params['task_id'] = NULL;
        $params['dubbizle_id'] = NULL;
        $params['customer_id'] = $customer->id;

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

                // $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $forwarded_message, FALSE, $message->id);
                $this->sendWithNewApi($user->phone, $user->whatsapp_number, $forwarded_message, NULL, $message->id);

                if ($second_message != '') {
                  // $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, FALSE, $message->id);
                  $this->sendWithNewApi($user->phone, $user->whatsapp_number, NULL, $second_message, $message->id);
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

              // $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $forwarded_message, FALSE, $message->id);
              $this->sendWithNewApi($user->phone, $user->whatsapp_number, $forwarded_message, NULL, $message->id);

              if ($second_message != '') {
                // $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, FALSE, $message->id);
                $this->sendWithNewApi($user->phone, $user->whatsapp_number, NULL, $second_message, $message->id);
              }
            }
          }
        }

        // Auto DND
        if (array_key_exists('message', $params) && strtoupper($params['message']) == 'DND') {
          if ($customer = Customer::find($params['customer_id'])) {
            $customer->do_not_disturb = 1;
            $customer->save();

            $dnd_params = [
               'number'       => NULL,
               'user_id'      => 6,
               'approved'     => 1,
               'status'       => 9,
               'customer_id'  => $customer->id,
               'message'      => 'Greetings from Sololuxury.co.in, based on your request we have now placed you on DND. You will no longer receive updates from us. We apologize for the inconvenience caused.'
             ];

            $auto_dnd_message = ChatMessage::create($dnd_params);

            // $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $dnd_params['message'], FALSE, $auto_dnd_message->id);
            $this->sendWithNewApi($customer->phone, $customer->whatsapp_number, $dnd_params['message'], NULL, $auto_dnd_message->id);
          }
        }

        // Auto Instruction
        if (array_key_exists('message', $params) && (preg_match("/price/i", $params['message']) || preg_match("/you photo/i", $params['message']) || preg_match("/pp/i", $params['message']) || preg_match("/how much/i", $params['message']) || preg_match("/cost/i", $params['message']) || preg_match("/rate/i", $params['message']))) {
          if ($customer = Customer::find($params['customer_id'])) {
            $two_hours = Carbon::now()->subHours(2);
            $latest_broadcast_message = ChatMessage::where('customer_id', $customer->id)->where('created_at', '>', $two_hours)->where('status', 8)->latest()->first();

            if ($latest_broadcast_message) {
              if (!$latest_broadcast_message->is_sent_broadcast_price()) {
                if ($latest_broadcast_message->hasMedia(config('constants.media_tags'))) {
                  $selected_products = [];

                  foreach ($latest_broadcast_message->getMedia(config('constants.media_tags')) as $image) {
                    $image_key = $image->getKey();
                    $mediable_type = "BroadcastImage";

                    $broadcast = BroadcastImage::with('Media')
                    ->whereRaw("broadcast_images.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
                    ->first();

                    if ($broadcast) {
                      $brod_products = json_decode($broadcast->products, true);

                      if (count($brod_products) > 0) {
                        foreach ($brod_products as $brod_pro) {
                          $selected_products[] = $brod_pro;
                        }
                      }
                    }
                  }

                  if (isset($broadcast)) {
                    $quick_lead = Leads::create([
                      'customer_id' => $customer->id,
                      'rating'  => 1,
                      'status'  => 3,
                      'assigned_user' => 6,
                      'selected_product'  => json_encode($selected_products),
                      'created_at'  => Carbon::now()
                    ]);

                    $requestData = new Request();
                    $requestData->setMethod('POST');
                    $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                    app('App\Http\Controllers\LeadsController')->sendPrices($requestData);

                    CommunicationHistory::create([
              				'model_id'		=> $latest_broadcast_message->id,
              				'model_type'	=> ChatMessage::class,
              				'type'				=> 'broadcast-prices',
              				'method'			=> 'whatsapp'
              			]);
                  } else {
                    // Instruction::create([
                    //   'customer_id' => $customer->id,
                    //   'instruction' => 'Please send the prices',
                    //   'category_id' => 1,
                    //   'assigned_to' => 7,
                    //   'assigned_from' => 6
                    // ]);
                  }
                }
              }
            }

            Instruction::create([
              'customer_id' => $customer->id,
              'instruction' => 'Please send the prices',
              'category_id' => 1,
              'assigned_to' => 7,
              'assigned_from' => 6
            ]);
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

          $message = ChatMessage::create($params);
          $model_type = 'leads';
          $model_id = $lead->id;
        }
      }

      // Auto Respond
      $today_date = Carbon::now()->format('Y-m-d');
      $time = Carbon::now();
      $morning = Carbon::create($time->year, $time->month, $time->day, 10, 0, 0);
      $not_morning = Carbon::create($time->year, $time->month, $time->day, 0, 0, 0);
      $evening = Carbon::create($time->year, $time->month, $time->day, 17, 30, 0);
      $not_evening = Carbon::create($time->year, $time->month, $time->day, 23, 59, 0);
      $saturday = Carbon::now()->endOfWeek()->subDay()->format('Y-m-d');
      $sunday = Carbon::now()->endOfWeek()->format('Y-m-d');

      $chat_messages_query = ChatMessage::where('customer_id', $params['customer_id'])->whereBetween('created_at', [$morning, $evening])->whereNotNull('number');
      $chat_messages_count = $chat_messages_query->count();

      $chat_messages_evening_query = ChatMessage::where('customer_id', $params['customer_id'])->where(function($query) use ($not_morning, $morning, $evening, $not_evening) {
        $query->whereBetween('created_at', [$not_morning, $morning])->orWhereBetween('created_at', [$evening, $not_evening]);
      })->whereNotNull('number');
      $chat_messages_evening_count = $chat_messages_evening_query->count();

      if ($chat_messages_count == 1) {
        $chat_messages_query_first = $chat_messages_query->first();
      }

      if ($chat_messages_evening_count == 1) {
        $chat_messages_evening_query_first = $chat_messages_evening_query->first();
      }

      if ($chat_messages_count == 1 && (isset($chat_messages_query_first) && $chat_messages_query_first->id == $message->id) && ($saturday != $today_date && $sunday != $today_date)) {
        $customer = Customer::find($params['customer_id']);
        $params = [
           'number'       => NULL,
           'user_id'      => 6,
           'approved'     => 1,
           'status'       => 9,
           'customer_id'  => $params['customer_id']
         ];

        if ($time->between($morning, $evening, true)) {
          $params['message'] = 'Hello we have received your message - and the concerned asscociate will revert asap - since the phone is connected to a server it shows online - messages read  24 / 7 - but the message is directed to the concerned associate and response us time is 60 minutes  .Pls. note that we do not answer calls on this number as its linked to our servers.';

          sleep(1);
          $additional_message = ChatMessage::create($params);
          // $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, FALSE, $additional_message->id);
          $this->sendWithNewApi($message->customer->phone, $customer->whatsapp_number, $additional_message->message, NULL, $additional_message->id);
        }
      } else if (($chat_messages_evening_count == 1 && (isset($chat_messages_evening_query_first) && $chat_messages_evening_query_first->id == $message->id)) || ($chat_messages_count == 1 && ($saturday == $today_date || $sunday == $today_date))) {
        $customer = Customer::find($params['customer_id']);
        $params = [
           'number'       => NULL,
           'user_id'      => 6,
           'approved'     => 1,
           'status'       => 9,
           'customer_id'  => $params['customer_id'],
           'message'      => 'Our office is currently closed - we work between 10 - 5.30 - Monday - Friday -  - if an associate is available - your messaged will be responded within 60 minutes or on the next working day -since the phone is connected to a server it shows online - messages read  24 / 7 - but the message is directed to the concerned associate shall respond accordingly.'
         ];

         sleep(1);
         $additional_message = ChatMessage::create($params);
         // $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, FALSE, $additional_message->id);
         $this->sendWithNewApi($message->customer->phone, $customer->whatsapp_number, $additional_message->message, NULL, $additional_message->id);
      }

      if ($data['data']['type'] == 'image') {
        $message->attachMedia($media,config('constants.media_tags'));
      }
    } else {
      $custom_data = json_decode($data['custom_data'], true);

      $chat_message = ChatMessage::find($custom_data['chat_message_id']);

      if ($chat_message) {
        $chat_message->sent = 1;
        $chat_message->save();
      }
    }

    return response("success", 200);
    }

    public function webhook(Request $request)
    {
      $data = $request->json()->all();

      file_put_contents(__DIR__."/webhook.txt", json_encode($data));

      // $to = str_replace('+', '', $data['data']['toNumber']);
  		$from = str_replace('@c.us', '', $data['messages'][0]['author']);
  		$text = $data['messages'][0]['body'];
      $supplier = $this->findSupplierByNumber($from);
      $user = $this->findUserByNumber($from);

      $params = [
        'number'    => $from,
        'message'   => '',
        'approved'  => $data['messages'][0]['fromMe'] ? 1 : 0,
        'status'    => $data['messages'][0]['fromMe'] ? 2 : 0
      ];

      // if ($data['messages'][0]['fromMe'] == false) {
        // if ($data['data']['type'] == 'text') {
          $params['message'] = $text;
        // }
        // else if ($data['data']['type'] == 'image') {
        //   $image_data = $data['data']['media']['preview']['image'];
        //   $image_path = public_path() . '/uploads/temp_image.png';
        //   $img = Image::make(base64_decode($image_data))->encode('jpeg')->save($image_path);
        //
        //   $media = MediaUploader::fromSource($image_path)->upload();
        //
        //   File::delete('uploads/temp_image.png');
        // }

        if ($user) {
          // $instruction = Instruction::where('assigned_to', $user->id)->latest()->first();
          // $myRequest = new Request();
          // $myRequest->setMethod('POST');
          // $myRequest->request->add(['remark' => $params['message'], 'id' => $instruction->id, 'module_type' => 'instruction', 'user_name' => "User from Whatsapp"]);
          //
          // app('App\Http\Controllers\TaskModuleController')->addRemark($myRequest);
          //
          // NotificationQueueController::createNewNotification([
          //   'message' => $params['message'],
          //   'timestamps' => ['+0 minutes'],
          //   'model_type' => Instruction::class,
          //   'model_id' =>  $instruction->id,
          //   'user_id' => '6',
          //   'sent_to' => $instruction->assigned_from,
          //   'role' => '',
          // ]);

          $params['erp_user'] = $user->id;
          $params['user_id'] = $user->id;

          if ($params['message'] != '' && (preg_match_all("/#([\d]+)/i", $params['message'], $match))) {
            $params['task_id'] = $match[1][0];
          }

          // $params = $this->modifyParamsWithMessage($params, $data);
          $message = ChatMessage::create($params);
          // $model_type = 'user';
          // $model_id = $user->id;
        }

        if ($supplier) {
          $params['erp_user'] = NULL;
          $params['task_id'] = NULL;
          $params['supplier_id'] = $supplier->id;

          $message = ChatMessage::create($params);
          // $model_type = 'supplier';
          // $model_id = $supplier->id;
        }
      // }

      return response('success', 200);
    }

    public function outgoingProcessed(Request $request)
    {
      $data = $request->json()->all();

      // file_put_contents(__DIR__."/outgoing.txt", json_encode($data));

      foreach ($data as $event) {
        $chat_message = ChatMessage::find($event['data']['reference']);

        if ($chat_message) {
          $chat_message->sent = 1;
          $chat_message->save();
        }
      }

      return response("success", 200);
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
        // 'message'         => 'nullable|required_without:image,images,screenshot_path|string',
//        'image'           => 'nullable|required_without:message',
//        'screenshot_path' => 'nullable|required_without:message',
        'customer_id'     => 'sometimes|nullable|numeric',
        'supplier_id'     => 'sometimes|nullable|numeric',
        'task_id'         => 'sometimes|nullable|numeric',
        'erp_user'        => 'sometimes|nullable|numeric',
        'status'          => 'required|numeric',
        'assigned_to'     => 'sometimes|nullable',
      ]);

      $data = $request->except( '_token');
      $data['user_id'] = Auth::id();
      $data['number'] = NULL;
      // $params['status'] = 1;

      if ($context == 'customer') {
        $data['customer_id'] = $request->customer_id;
        $module_id = $request->customer_id;
      } elseif ($context == "purchase") {
        $data['purchase_id'] = $request->purchase_id;
        $module_id = $request->purchase_id;
      } elseif ($context == 'supplier') {
        $data['supplier_id'] = $request->supplier_id;
        $module_id = $request->supplier_id;
      } elseif ($context == 'task') {
        $data['task_id'] = $request->task_id;
        $task = Task::find($request->task_id);

        $data['message'] = "#" . $data['task_id'] . ". " . $data['message'];

        if ($task->assign_from == Auth::id()) {
          $data['erp_user'] = $task->assign_to;
        } else {
          $data['erp_user'] = $task->assign_from;
        }

        $module_id = $request->task_id;

        // if ($data['erp_user'] != Auth::id()) {
        //   $data['status'] = 0;
        // }
      } elseif ($context == 'user') {
        $data['erp_user'] = $request->user_id;
        $module_id = $request->user_id;
      } elseif ($context == 'dubbizle') {
        $data['dubbizle_id'] = $request->dubbizle_id;
        $module_id = $request->dubbizle_id;
      }

      $chat_message = ChatMessage::create($data);

      $data['status'] = 1;

      // if ($context == 'task' && $data['erp_user'] != Auth::id()) {
      //   $data['erp_user'] = Auth::id();
      //
      //   $another_message = ChatMessage::create($data);
      // }

      if ($request->hasFile('image')) {
        $media = MediaUploader::fromSource($request->file('image'))->upload();
        $chat_message->attachMedia($media,config('constants.media_tags'));

        // if ($context == 'task' && $data['erp_user'] != Auth::id()) {
        //   $another_message->attachMedia($media,config('constants.media_tags'));
        // }
      }

      if ($request->images) {
        foreach (json_decode($request->images) as $image) {
          $media = Media::find($image);
          $chat_message->attachMedia($media,config('constants.media_tags'));

          // if ($context == 'task' && $data['erp_user'] != Auth::id()) {
          //   $another_message->attachMedia($media,config('constants.media_tags'));
          // }
        }
      }

      if ($request->screenshot_path != '') {
        $image_path = public_path() . '/uploads/temp_screenshot.png';
        $img = substr($request->screenshot_path, strpos($request->screenshot_path, ",")+1);
        $img = Image::make(base64_decode($img))->encode('png')->save($image_path);

        $media = MediaUploader::fromSource($image_path)->upload();
        $chat_message->attachMedia($media,config('constants.media_tags'));

        // if ($context == 'task' && $data['erp_user'] != Auth::id()) {
        //   $another_message->attachMedia($media,config('constants.media_tags'));
        // }

        File::delete('uploads/temp_screenshot.png');
      }

      if (Auth::id() == 6) {
        $myRequest = new Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        $this->approveMessage($context, $myRequest);
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
      if ($request->customerId) {
        $column = 'customer_id';
        $value = $request->customerId;
      } else if ($request->supplierId) {
        $column = 'supplier_id';
        $value = $request->supplierId;
      } else if ($request->taskId) {
        $column = 'task_id';
        $value = $request->taskId;
      } else if ($request->erpUser) {
        $column = 'erp_user';
        $value = $request->erpUser;
      } else if ($request->dubbizleId) {
        $column = 'dubbizle_id';
        $value = $request->dubbizleId;
      } else {
        $column = 'customer_id';
        $value = $request->customerId;
      }


      $messages = ChatMessage::select(['id', 'customer_id', 'number', 'user_id', 'erp_user', 'assigned_to', 'approved', 'status', 'sent', 'error_status', 'created_at', 'media_url', 'message'])->where($column, $value)->latest();

      if (Setting::get('show_automated_messages') == 0) {
        $messages = $messages->where('status', '!=', 9);
      }

      if ($request->erpUser) {
        $messages = $messages->whereNull('task_id');
      }
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
          'erp_user' => $message->erp_user,
          'sent'    => $message->sent,
          'error_status'    => $message->error_status
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
               'status'       => 9,
               'customer_id'  => $message->customer_id,
               'message'      => 'This Number is just for whats app messages and NOT CALLS , for calls pls. use our toll free number 0008000401700 - ( care to be taken not to dial with 91 or  + 91 ). Pls. leave a message if you cannot connect to our toll free number and we will call you back at the earliest.'
             ];

            $additional_message = ChatMessage::create($params);

            if ($customer->whatsapp_number == '919152731483') {
              $data = $this->sendWithNewApi($message->customer->phone, $customer->whatsapp_number, $additional_message->message, NULL, $additional_message->id);
            } else {
              $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, TRUE, $additional_message->id);
            }

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
                 'status'       => 9,
                 'customer_id'  => $message->customer_id,
                 'message'      => 'Our whatsapp number has changed'
               ];

              $additional_message = ChatMessage::create($params);

              if ($default_api->number == '919152731483') {
                $data = $this->sendWithNewApi($customer->phone, $default_api->number, $additional_message->message, NULL, $additional_message->id);
              } else {
                $this->sendWithWhatsApp($customer->phone, $default_api->number, $additional_message->message, TRUE, $additional_message->id);
              }

              sleep(5);

              CommunicationHistory::create([
        				'model_id'		=> $customer->id,
        				'model_type'	=> Customer::class,
        				'type'				=> 'number-change',
        				'method'			=> 'whatsapp'
        			]);
            }
          }

          if (isset($customer)) {
            $phone = $customer->phone;
            $whatsapp_number = $customer->whatsapp_number;
          } else {
            $customer = Customer::find($message->customer_id);
            $phone = $customer->phone;
            $whatsapp_number = $customer->whatsapp_number;
          }
        } else if ($context == 'supplier') {
          $supplier = Supplier::find($message->supplier_id);
          $phone = $supplier->default_phone;
          $whatsapp_number = $supplier->whatsapp_number;
        } else if ($context == 'task') {
          $user = User::find($message->user_id);
          $phone = $user->phone;
          $whatsapp_number = $user->whatsapp_number;
        } else if ($context == 'user') {
          $user = User::find($message->erp_user);
          $phone = $user->phone;
          $whatsapp_number = $user->whatsapp_number;
        } else if ($context == 'dubbizle') {
          $dubbizle = Dubbizle::find($message->dubbizle_id);
          $phone = $dubbizle->phone_number;
          $whatsapp_number = '919152731483';
        }

        $data = '';
        if ($message->message != '') {

          if ($context == 'supplier' || $context == 'task') {
            $this->sendWithThirdApi($phone, $whatsapp_number, $message->message, NULL, $message->id);
          } else {
            if ($whatsapp_number == '919152731483') {
              $data = $this->sendWithNewApi($phone, $whatsapp_number, $message->message, NULL, $message->id);
            } else {
              $this->sendWithWhatsApp($phone, $whatsapp_number, $message->message, FALSE, $message->id);
            }
          }
        }

        if ($message->media_url != '') {

          if ($whatsapp_number == '919152731483') {
            $data = $this->sendWithNewApi($phone, $whatsapp_number, NULL, $message->media_url, $message->id);
          } else {
            $this->sendWithWhatsApp($phone, $whatsapp_number, $message->media_url, FALSE, $message->id);
          }
        }

        if ($images = $message->getMedia(config('constants.media_tags'))) {
          foreach ($images as $image) {
            $send = str_replace(' ', '%20', $image->getUrl());

            if ($whatsapp_number == '919152731483') {
              $data = $this->sendWithNewApi($phone, $whatsapp_number, NULL, $image->getUrl(), $message->id);
            } else {
              $this->sendWithWhatsApp($phone, $whatsapp_number, $send, FALSE, $message->id);
            }
          }
        }

        $message->update([
          'approved' => 1,
          'status'   => 2,
          'created_at'  => Carbon::now()
        ]);

        return response()->json([
          'data'  => $data
        ]);
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
        'rating'          => 'sometimes|nullable|numeric',
        'gender'          => 'sometimes|nullable|string',
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
      foreach (json_decode($request->linked_images) as $key => $id) {
        $broadcast_image = BroadcastImage::find($id);

        if ($broadcast_image->hasMedia(config('constants.media_tags'))) {
          foreach ($broadcast_image->getMedia(config('constants.media_tags')) as $key2 => $brod_image) {
            $content['linked_images'][$key + $key2]['key'] = $brod_image->getKey();
            $content['linked_images'][$key + $key2]['url'] = $brod_image->getUrl();
          }
        }

      }
      // $content['linked_images'] = json_decode($request->linked_images);
    }

    if ($request->to_all || $request->moduletype == 'customers') {
      $minutes = round(60 / $frequency);
      $max_group_id = MessageQueue::max('group_id') + 1;


      $data = Customer::whereNotNull('phone')->where('do_not_disturb', 0);

      if ($request->rating != '') {
        $data = $data->where('rating', $request->rating);
      }

      if ($request->gender != '') {
        $data = $data->where('gender', $request->gender);
      }

      $data = $data->get()->groupBy('whatsapp_number');

      foreach ($data as $whatsapp_number => $customers) {
        $now = $request->sending_time ? Carbon::parse($request->sending_time) : Carbon::now();
        $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
        $evening = Carbon::create($now->year, $now->month, $now->day, 18, 0, 0);

        if ($whatsapp_number == '919152731486') {
          foreach ($customers as $customer) {
            if (!$now->between($morning, $evening, true)) {
              if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                // add day
                $now->addDay();
                $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $evening = Carbon::create($now->year, $now->month, $now->day, 18, 0, 0);
              } else {
                // dont add day
                $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $evening = Carbon::create($now->year, $now->month, $now->day, 18, 0, 0);
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
                $evening = Carbon::create($now->year, $now->month, $now->day, 18, 0, 0);
              } else {
                // dont add day

                $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $evening = Carbon::create($now->year, $now->month, $now->day, 18, 0, 0);
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
      $evening = Carbon::create($now->year, $now->month, $now->day, 18, 0, 0);
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
              $evening = Carbon::create($now->year, $now->month, $now->day, 18, 0, 0);
            } else {
              // dont add day
              $now = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
              $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
              $evening = Carbon::create($now->year, $now->month, $now->day, 18, 0, 0);
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

  public function sendWithNewApi($number, $whatsapp_number = null, $message = null, $file = null, $chat_message_id = null, $enqueue = 'opportunistic')
	{
    $configs = \Config::get("wassenger.api_keys");
    $encodedNumber = "+" . $number;
    $encodedText = $message;
    $wa_token = $configs[0]['key'];

    if ($whatsapp_number != NULL) {
      foreach ($configs as $key => $config) {
        if ($config['number'] == $whatsapp_number) {
          $wa_device = $config['device'];

          break;
        }

        $wa_device = $configs[0]['device'];
      }
    } else {
      $wa_device = $configs[0]['device'];
    }

    if ($file != NULL) {
      $file_exploded = explode('/', $file);
      $encoded_part = str_replace('%25', '%', urlencode(str_replace(' ', '%20', $file_exploded[count($file_exploded) - 1])));
      array_pop($file_exploded);
      array_push($file_exploded, $encoded_part);

      $file_encoded = implode('/', $file_exploded);

      $array = [
        'url' => "$file_encoded",
        // 'reference' => "$chat_message_id"
      ];

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.wassenger.com/v1/files?reference=$chat_message_id",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($array),
        CURLOPT_HTTPHEADER => array(
          "content-type: application/json",
          "token: $wa_token"
        ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);
      // throw new \Exception("cURL Error #: whatttt");
      if ($err) {
        throw new \Exception("cURL Error #:" . $err);
      } else {
        $result = json_decode($response, true);

        if (array_key_exists('status', $result)) {
          if ($result['status'] == 409) {
            $image_id = $result['meta']['file'];
          } else {
            throw new \Exception("Something was wrong with image: " . $result['message']);
          }
        } else {
          $image_id = $result[0]['id'];
        }
      }
    }


    $array = [
      'phone' => $encodedNumber,
      'message' => (string) $encodedText,
      'reference' => (string) $chat_message_id,
      'device'  => "$wa_device",
      'enqueue' => "$enqueue",
    ];

    if (isset($image_id)) {
      $array['media'] = [
        'file'  => "$image_id"
      ];
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.wassenger.com/v1/messages",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 120,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($array),
      CURLOPT_HTTPHEADER => array(
        "content-type: application/json",
        "token: $wa_token"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($err) {
      throw new \Exception("cURL Error #:" . $err);
    } else {
      $result = json_decode($response, true);

      if ($http_code != 201) {
        throw new \Exception("Something was wrong with message: " . $response);
      }
    }

    return $result;
	}

  public function sendWithThirdApi($number, $whatsapp_number = null, $message = null, $file = null, $chat_message_id = null, $enqueue = 'opportunistic')
	{
    // $configs = \Config::get("wassenger.api_keys");
    $encodedNumber = "+" . $number;
    $encodedText = $message;
    // $wa_token = $configs[0]['key'];
    $instanceId = "43254";
    $token = "2l4boog1xzk3tr43";

    // throw new \Exception("Yesah");

    // if ($whatsapp_number != NULL) {
    //   foreach ($configs as $key => $config) {
    //     if ($config['number'] == $whatsapp_number) {
    //       $wa_device = $config['device'];
    //
    //       break;
    //     }
    //
    //     $wa_device = $configs[0]['device'];
    //   }
    // } else {
    //   $wa_device = $configs[0]['device'];
    // }

    // if ($file != NULL) {
    //   $file_exploded = explode('/', $file);
    //   $encoded_part = str_replace('%25', '%', urlencode(str_replace(' ', '%20', $file_exploded[count($file_exploded) - 1])));
    //   array_pop($file_exploded);
    //   array_push($file_exploded, $encoded_part);
    //
    //   $file_encoded = implode('/', $file_exploded);
    //
    //   $array = [
    //     'url' => "$file_encoded",
    //     // 'reference' => "$chat_message_id"
    //   ];
    //
    //   $curl = curl_init();
    //
    //   curl_setopt_array($curl, array(
    //     CURLOPT_URL => "https://api.wassenger.com/v1/files?reference=$chat_message_id",
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => "",
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 120,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => "POST",
    //     CURLOPT_POSTFIELDS => json_encode($array),
    //     CURLOPT_HTTPHEADER => array(
    //       "content-type: application/json",
    //       "token: $wa_token"
    //     ),
    //   ));
    //
    //   $response = curl_exec($curl);
    //   $err = curl_error($curl);
    //
    //   curl_close($curl);
    //   // throw new \Exception("cURL Error #: whatttt");
    //   if ($err) {
    //     throw new \Exception("cURL Error #:" . $err);
    //   } else {
    //     $result = json_decode($response, true);
    //
    //     if (array_key_exists('status', $result)) {
    //       if ($result['status'] == 409) {
    //         $image_id = $result['meta']['file'];
    //       } else {
    //         throw new \Exception("Something was wrong with image: " . $result['message']);
    //       }
    //     } else {
    //       $image_id = $result[0]['id'];
    //     }
    //   }
    // }


    $array = [
      'phone' => $encodedNumber,
      'body' => (string) $encodedText,
      // 'reference' => (string) $chat_message_id,
      // 'device'  => "$wa_device",
      // 'enqueue' => "$enqueue",
    ];

    // if (isset($image_id)) {
    //   $array['media'] = [
    //     'file'  => "$image_id"
    //   ];
    // }

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.chat-api.com/instance$instanceId/sendMessage?token=$token",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($array),
      CURLOPT_HTTPHEADER => array(
        "content-type: application/json",
        // "token: $wa_token"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    // $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($err) {
      throw new \Exception("cURL Error #:" . $err);
    } else {
      $result = json_decode($response, true);
      // throw new \Exception("Something was wrong with message: " . $response);
      if (!$result['sent']) {
        throw new \Exception("Something was wrong with message: " . $response);
      }
    }

    return $result;
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

    public function fixMessageError(Request $request, $id)
    {
      $chat_message = ChatMessage::find($id);

      if ($customer = Customer::find($chat_message->customer_id)) {
        $customer->is_error_flagged = 0;
        $customer->save();

        $messages = ChatMessage::where('customer_id', $customer->id)->where('error_status', '!=', 0)->get();

        foreach ($messages as $message) {
          $message->error_status = 0;
          $message->save();
        }
      }

      return response('success');
    }

    public function resendMessage(Request $request, $id)
    {
      $chat_message = ChatMessage::find($id);

      if ($customer = Customer::find($chat_message->customer_id)) {
        $params = [
           'number'       => NULL,
           'user_id'      => Auth::id(),
           'approved'     => 1,
           'status'       => 2,
           'customer_id'  => $customer->id,
           'message'      => $chat_message->message
         ];

        $additional_message = ChatMessage::create($params);

        if ($additional_message->message != '') {
          $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $additional_message->message, TRUE, $additional_message->id);
        }

        if ($chat_message->hasMedia(config('constants.media_tags'))) {
          foreach ($chat_message->getMedia(config('constants.media_tags')) as $image) {
            $additional_message->attachMedia($image, config('constants.media_tags'));

            $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, str_replace(' ', '%20', $image->getUrl()), TRUE, $additional_message->id);
          }
        }
      }

      return response('success');
    }
}
