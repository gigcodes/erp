<?php

namespace App\Http\Controllers;

use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
use App\Jobs\SendMessageToAll;
use App\Jobs\SendMessageToSelected;
use App\Category;
use App\Notification;
use App\Leads;
use App\Order;
use App\Status;
use App\Setting;
use App\User;
use App\Brand;
use App\Product;
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

      if ($lead) {
        $params['lead_id'] = $lead->id;

        if ($lead->customer) {
          $params['customer_id'] = $lead->customer->id;
        }

        $params = $this->modifyParamsWithMessage($params, $data);
        $message = ChatMessage::create($params);
        $model_type = 'leads';
        $model_id = $lead->id;
        $lead->update([
            'whatsapp_number' => $to
        ]);
      } else {
        $order= $this->findOrderByNumber($from);

        if ($order) {
          $params['lead_id'] = null;
          $params['order_id'] = $order->id;

          if ($order->customer) {
            $params['customer_id'] = $order->customer->id;
          }

          $params = $this->modifyParamsWithMessage($params, $data);
          $message = ChatMessage::create($params);
          $model_type = 'order';
          $model_id = $order->id;
          $order->update([
              'whatsapp_number' => $to
          ]);
        } else {
          $purchase = $this->findPurchaseByNumber($from);

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
          } else {
            // placeholder
          }
        }
      }

      if (!isset($order) && !isset($lead) && !isset($user) && !isset($purchase)) {
          $modal_type = 'leads';
          // $new_name = "whatsapp lead " . uniqid( TRUE );
          $user = User::get()[0];

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


          // NotificationQueueController::createNewNotification([
          //   'message' => 'Reminder for Instructions',
          //   'timestamps' => ['+10 minutes'],
          //   'model_type' => Instruction::class,
          //   'model_id' =>  $instruction->id,
          //   'user_id' => Auth::id(),
          //   'sent_to' => $instruction->assigned_to,
          //   'role' => '',
          // ]);
          $params['lead_id'] = $lead->id;
          $params['customer_id'] = $customer->id;
          $params = $this->modifyParamsWithMessage($params, $data);
          $message = ChatMessage::create($params);
          $model_type = 'leads';
          $model_id = $lead->id;
      }

      // Auto Respond
      $today_date = Carbon::now()->format('Y-m-d');
      $chat_messages_count = ChatMessage::where('customer_id', $params['customer_id'])->where('created_at', 'LIKE', "%$today_date%")->whereNotNull('number')->count();
      $chat_messages_evening_count = ChatMessage::where('customer_id', $params['customer_id'])->where('created_at', '>', "$today_date 17:30")->whereNotNull('number')->count();

      if ($chat_messages_count == 1) {
        $time = Carbon::now();
        $morning = Carbon::create($time->year, $time->month, $time->day, 10, 0, 0);
        $evening = Carbon::create($time->year, $time->month, $time->day, 17, 30, 0);

        $customer = Customer::find($params['customer_id']);
        $params = [
           'number'       => NULL,
           'user_id'      => 6,
           'approved'     => 1,
           'status'       => 2,
           'customer_id'  => $params['customer_id']
         ];

         if (!$time->between($morning, $evening, true)) {
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
           'status'       => 2,
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
    /**
     * Send message
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request, $context)
    {
	   $data = $request->all();
       try {
            $params = [];
           if ($context == "leads") {
             $lead = Leads::findOrFail( $data['lead_id'] );
             $model_type = 'leads';
             $model_id = $lead->id;
             $params = [
                'lead_id' => $lead->id,
                'number' => NULL,
                'user_id' => Auth::id()
               ];
              if ($lead->customer) {
                $params['customer_id'] = $lead->customer->id;
              }
            } elseif ($context == "orders") {
             $order = Order::findOrFail( $data['order_id'] );
             $model_type = 'order';
             $model_id = $order->id;
             $params = [
                'order_id' => $order->id,
                'number' => NULL,
                'user_id' => Auth::id()
              ];

              if ($order->customer) {
                $params['customer_id'] = $order->customer->id;
              }
            } elseif ($context == "customer") {
              $model_type = 'customer';
              $model_id = $data['customer_id'];
              $params = [
                 'number' => NULL,
                 'user_id' => Auth::id(),
                 'customer_id'  => $data['customer_id']
               ];
            } elseif ($context == "purchase") {
              $model_type = 'purchase';
              $model_id = $data['purchase_id'];
              $params = [
                 'number' => NULL,
                 'user_id' => Auth::id(),
                 'purchase_id' => $model_id
               ];
            }
            if (isset($data['message'])) {
                $params['message']  = $data['message'];
            } else { // media message
                $files = \Input::file("media");
                if ($files) {
                  foreach ($files as $media) {
                    if (!$media->isValid()) {
                      \Log::error(sprintf("sendMessage media invalid"));
                      continue;
                    }
                    $extension = $media->guessExtension();
                    if ( $extension == "jpeg" ) {
                        $extension = "jpg";
                    }
                    $fileName = uniqid(TRUE).".".$extension;
                    $media->move(\Config::get("apiwha.media_path"), $fileName);

                    $url = implode("/", array( \Config::get("app.url"), "apiwha", "media", $fileName ));
                    $params['media_url'] =$url;
                  }
                }
            }

            $params['status'] = 1;
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
            //   'model_type' => $model_type,
  		      //   'model_id' =>  $model_id,
      		  //   'user_id' => Auth::id(),
      		  //   'sent_to' => '',
      		  //   'role' => 'Admin',
      	    // ]);
        } catch (\Exception $ex) {
            return response($ex->getMessage(), 500);
        }


       return response($message);
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

        $this->approveMessage($request->moduletype, $myRequest);

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

      return response('success');
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
       if ($context == "leads") {
            $id = $request->get("leadId");
            $model_type = 'leads';
            $params['lead_id'] = $id;
	        $messages = ChatMessage::where('lead_id', '=', $id);
       } elseif ($context == "orders") {
            $id = $request->get("orderId");
            $model_type = 'order';
            $params['order_id'] = $id;
	        $messages = ChatMessage::where('order_id', '=', $id);
        } elseif ($context == 'purchase') {
          $id = $request->get("purchaseId");
          $model_type = 'purchase';
          $params['purchase_id'] = $id;
          $messages = ChatMessage::where('purchase_id', '=', $id);
        }
        if ($request->get("elapse")) {
            $elapse = (int) $request->get("elapse");
           $date = new \DateTime;
           $date->modify(sprintf("-%s seconds", $elapse));
           $messages = $messages->where('created_at', '>=', $date->format('Y-m-d H:i:s'));
        }
	   $result = [];
	   foreach ($messages->get() as $message) {
         $received = false;
         if (!is_null($message['number'])) {
            $received = true;
         }
         $messageParams = [
                'id' => $message['id'],
                'received' =>$received,
                'number' => $message['number'],
                'created_at' => Carbon::parse($message['created_at'])->format('Y-m-d H:i:s'),
                // 'date' => $this->formatChatDate( $message['created_at'] ),
                'approved' => $message['approved'],
                'status'  => $message['status'],
                'user_id' => $message['user_id']
         ];
         if ($message['media_url']) {
            $messageParams['media_url'] = $message['media_url'];
            $headers = get_headers($message['media_url'], 1);
            $messageParams['content_type'] = $headers["Content-Type"][1];
         }
         if ($message['message']) {
            $messageParams['message'] = $message['message'];
         }
         if ($message->getMedia(config('constants.media_tags'))->first()) {
           $images_array = [];
           foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
             $temp_image = [
               'key'          => $image->getKey(),
               'image'        => $image->getUrl(),
               'product_id'   => '',
               'special_price'=> '',
               'size'         => ''
             ];

             $product_image = Product::with('Media')->whereHas('Media', function($q) use($image) {
                                 $q->where('media.id', $image->getKey());
                               })->first();
             if ($product_image) {
               $temp_image['product_id'] = $product_image->id;
               $temp_image['special_price'] = $product_image->price_special;

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

     $messages = Message::where('moduleid','=', $id)->where('moduletype','=', $model_type)->orderBy("created_at", 'desc')->get();
     foreach ($messages->toArray() as $key => $message) {
       $images_array = [];
       if ($images = $messages[$key]->getMedia(config('constants.media_tags'))) {
         foreach ($images as $image) {
           $temp_image = [
             'key'          => $image->getKey(),
             'image'        => $image->getUrl(),
             'product_id'   => '',
             'special_price'=> '',
             'size'         => ''
           ];

           $product_image = Product::with('Media')->whereHas('Media', function($q) use($image) {
                               $q->where('media.id', $image->getKey());
                             })->first();
           if ($product_image) {
             $temp_image['product_id'] = $product_image->id;
             $temp_image['special_price'] = $product_image->price_special;

             if ($product_image->size != NULL) {
               $temp_image['size'] = $product_image->size;
             } else {
               $temp_image['size'] = (string) $product_image->lmeasurement . ', ' . (string) $product_image->hmeasurement . ', ' . (string) $product_image->dmeasurement;
             }
           }

           array_push($images_array, $temp_image);
         }
       }

       $message['images'] = $images_array;
       array_push($result, $message);
     }

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
      $messages = ChatMessage::where('customer_id', $request->customerId)->latest();

      if ($request->get("elapse")) {
        $elapse = (int) $request->get("elapse");
        $date = new \DateTime;
        $date->modify(sprintf("-%s seconds", $elapse));
        $messages = $messages->where('created_at', '>=', $date->format('Y-m-d H:i:s'));
      }

      foreach ($messages->get() as $message) {
        $received = false;

        if (!is_null($message['number'])) {
          $received = true;
        }

        $messageParams = [
          'id' => $message['id'],
          'received' => $received,
          'number' => $message['number'],
          'created_at' => Carbon::parse($message['created_at'])->format('Y-m-d H:i:s'),
          'approved' => $message['approved'],
          'status'  => $message['status'],
          'user_id' => $message['user_id'],
          'sent'    => $message['sent']
        ];

        if ($message['media_url']) {
          $messageParams['media_url'] = $message['media_url'];
          $headers = get_headers($message['media_url'], 1);
          $messageParams['content_type'] = $headers["Content-Type"][1];
        }

        if ($message['message']) {
          $messageParams['message'] = $message['message'];
        }

        if ($message->getMedia(config('constants.media_tags'))->first()) {
          $images_array = [];

          foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
            $temp_image = [
              'key'          => $image->getKey(),
              'image'        => $image->getUrl(),
              'product_id'   => '',
              'special_price'=> '',
              'size'         => ''
            ];

            $product_image = Product::with('Media')->whereHas('Media', function($q) use($image) {
               $q->where('media.id', $image->getKey());
            })->select(['id', 'price_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

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

      $messages = Message::where('customer_id', $request->customerId)->orderBy("created_at", 'desc')->get();

      foreach ($messages->toArray() as $key => $message) {
        $images_array = [];

        if ($images = $messages[$key]->getMedia(config('constants.media_tags'))) {
          foreach ($images as $image) {
            $temp_image = [
            'key'          => $image->getKey(),
            'image'        => $image->getUrl(),
            'product_id'   => '',
            'special_price'=> '',
            'size'         => ''
            ];

            $product_image = Product::with('Media')->whereHas('Media', function($q) use($image) {
              $q->where('media.id', $image->getKey());
            })->select(['id', 'price_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

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
        }

        $message['images'] = $images_array;
        array_push($result, $message);
      }

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
          }
        }

        $send = $message->message;
        if (is_null($send)) {
          $send = $message->media_url;

          if (is_null($send)) {
            if ($images = $message->getMedia(config('constants.media_tags'))) {
              foreach ($images as $image) {
                $send = str_replace(' ', '%20', $image->getUrl());

                if ($context == "leads") {
                  $lead = Leads::find($message->lead_id);
                  $this->sendWithWhatsApp( $lead->contactno, $lead->whatsapp_number, $send, TRUE, $message->id);
                } elseif ( $context == "orders") {
                  $order = Order::find($message->order_id);
                  $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send, TRUE, $message->id);
                } elseif ($context == "customer") {
                  $customer = Customer::find($message->customer_id);
                  $this->sendWithWhatsApp( $message->customer->phone,$customer->whatsapp_number, $send, TRUE, $message->id);
                } elseif ($context == 'purchase') {
                  $purchase = Purchase::find($message->purchase_id);
                  $this->sendWithWhatsApp($purchase->supplier_phone,$purchase->whatsapp_number, $send, TRUE, $message->id);
                }
              }
            }
          } else {
            if ($context == "leads") {
              $lead = Leads::find($message->lead_id);
              $this->sendWithWhatsApp( $lead->contactno, $lead->whatsapp_number, $send, TRUE, $message->id);
            } elseif ( $context == "orders") {
              $order = Order::find($message->order_id);
              $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send, TRUE, $message->id);
            } elseif ($context == "customer") {
              $customer = Customer::find($message->customer_id);
              $this->sendWithWhatsApp( $message->customer->phone,$customer->whatsapp_number, $send, TRUE, $message->id);
            } elseif($context == 'purchase') {
              $purchase = Purchase::find($message->purchase_id);
              $this->sendWithWhatsApp($purchase->supplier_phone,$purchase->whatsapp_number, $send, TRUE, $message->id);
            }
          }
        } else {
          if ($context == "leads") {
            $lead = Leads::find($message->lead_id);
            $this->sendWithWhatsApp($lead->contactno, $lead->whatsapp_number, $send, TRUE, $message->id);
          } elseif ( $context == "orders") {
            $order = Order::find($message->order_id);
            $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send, TRUE, $message->id);
          } elseif ($context == "customer") {
            $customer = Customer::find($message->customer_id);
            $this->sendWithWhatsApp($message->customer->phone,$customer->whatsapp_number, $send, TRUE, $message->id);
          } elseif ($context == 'purchase') {
            $purchase = Purchase::find($message->purchase_id);
            $this->sendWithWhatsApp($purchase->supplier_phone,$purchase->whatsapp_number, $send, TRUE, $message->id);
          }
        }

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
        'message'       => 'required_without:images',
        'images'        => 'required_without:message',
        'images.*'      => 'required_without:message',
        'file'          => 'sometimes|mimes:xlsx,xls',
        'sending_time'  => 'required|date'
      ]);
    }

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

    if ($request->to_all || $request->moduletype == 'customers') {
      $now = $request->sending_time ? Carbon::parse($request->sending_time) : Carbon::now();
      $minutes = 5;
      $max_group_id = MessageQueue::max('group_id') + 1;

      $data = Customer::whereNotNull('phone')->where('do_not_disturb', 0)->chunk(20, function ($customers) use ($content, $now, &$minutes, $max_group_id) {
        foreach ($customers as $customer) {
          // SendMessageToAll::dispatch(Auth::id(), $customer, $content)
          //                 ->delay($now->addMinutes($minutes))
          //                 ->onQueue('sending');



          MessageQueue::create([
            'user_id'       => Auth::id(),
            'customer_id'   => $customer->id,
            'phone'         => NULL,
            'type'          => 'message_all',
            'data'          => json_encode($content),
            'sending_time'  => $now,
            'group_id'      => $max_group_id
          ]);
        }

        $now->addMinutes($minutes);
      });
    } else {
      $now = $now = $request->sending_time ? Carbon::parse($request->sending_time) : Carbon::now();
      $minutes = 0;
      $count = 0;
      $max_group_id = MessageQueue::max('group_id') + 1;
      $array = Excel::toArray(new CustomerNumberImport, $request->file('file'));

      foreach ($array as $item) {
        foreach ($item as $it) {
          if ($count == 20) {
            // $minutes += 5;
            $now->addMinutes(5);
            $count = 0;
          }
          $number = (int)$it[0];

          MessageQueue::create([
            'user_id'       => Auth::id(),
            'customer_id'   => NULL,
            'phone'         => $number,
            'type'          => 'message_selected',
            'data'          => json_encode($content),
            'sending_time'  => $now,
            'group_id'      => $max_group_id
          ]);

          // SendMessageToSelected::dispatch($number, $content)
          //                       ->delay($now->addMinutes($minutes))
          //                       ->onQueue('sending');

          $count++;
        }
      }
    }

    return redirect()->route('customer.index')->with('success', 'Messages are being sent in the background!');
  }

  public function stopAll() {
    $message_queues = MessageQueue::where('sent', 0)->where('status', 0)->get();

    foreach ($message_queues as $message_queue) {
      $message_queue->status = 1;
      $message_queue->save();
    }

    return redirect()->route('customer.index')->with('success', 'Messages stopped processing!');
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

    foreach (\Config::get("apiwha.api_keys") as $config_key) {
      if ($config_key['number'] == $number) {
        return;
      }
    }

        $curl = curl_init();
        if (is_null($sendNumber)) {
            $keys = \Config::get("apiwha.api_keys");
            $key = $keys[0]['key'];
        } else {
            $config = $this->getWhatsAppNumberConfig($sendNumber);
            $key = $config['key'];
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
        // $moduleid = $request->get('moduleid');
        // $moduletype = $request->get('moduletype');
        $message->save();

      // if( $message->status == '5' ) {
		  //   NotificationQueueController::createNewNotification( [
			//     'message'    => 'Message was read : ' . $message->message,
			//     'timestamps' => [ '+0 minutes' ],
			//     'model_type' => $moduletype,
			//     'model_id'   => $moduleid,
			//     'user_id'    => Auth::id(),
			//     'sent_to'    => '',
			//     'role'       => 'Admin',
		  //   ] );
	    // }

      // if( $message->status == '6' ) {
      //   if ($notifications = PushNotification::where('model_id', $moduleid)->where('model_type', $moduletype)->get()) {
      //     foreach ($notifications as $notification) {
      //       $notification->isread = 1;
      //       $notification->save();
      //     }
      //   }
      //
      //   if ($notifications_queue = NotificationQueue::where('model_id', $moduleid)->where('model_type', $moduletype)->get()) {
      //     foreach ($notifications_queue as $notification) {
      //       $notification->delete();
      //     }
      //   }
      //
		  //   NotificationQueueController::createNewNotification( [
			//     'message'    => 'Message Sent : ' . $message->message,
			//     'timestamps' => [ '+0 minutes' ],
			//     'model_type' => $moduletype,
			//     'model_id'   => $moduleid,
			//     'user_id'    => Auth::id(),
			//     'sent_to'    => '6',
			//     'role'       => 'Admin',
		  //   ] );
	    // }

	    // return redirect('/'. $moduletype.'/'.$moduleid);
    }
}
