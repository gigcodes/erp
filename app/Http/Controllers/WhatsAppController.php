<?php

namespace App\Http\Controllers;

use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers;
use App\ChatMessage;
use App\PushNotification;
use App\NotificationQueue;
use App\Customer;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;


class WhatsAppController extends FindByNumberController
{
    /**
     * Incoming message URL for whatsApp
     *
     * @return \Illuminate\Http\Response
     */
    public function incomingMessage(Request $request)
    {
		//$data = json_decode($request->get("data"), TRUE);
		$data = $request->json()->all();
		$to = $data['to'];
		$from = $data['from'];
		$text = $data['text'];
		$lead = $this->findLeadByNumber( $from );
    // $leads = Leads::where('contactno', '=', $from)->get();

        //save to leads
        $params = [
            'number' => $from
        ];

        // if ( count($leads) > 0 ) {
        //   foreach ($leads as $lead) {
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
          $order= $this->findOrderByNumber( $from );
              if ( $order ) {
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

              }
        }

        //   }
        // }

        //save to orders
        // $orders = Order::where('contact_detail', '=', $from)->get();

        // if (count($orders) > 0) {
        //   foreach ($orders as $order) {

        //   }
        // }


        if (!$orders && !$leads) {
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
            $params['lead_id'] = $lead->id;
            $params['customer_id'] = $customer->id;
            $params = $this->modifyParamsWithMessage($params, $data);
            $message = ChatMessage::create($params);
            $modal_type = 'leads';
            $modal_id = $lead->id;
        }

        NotificationQueueController::createNewNotification([
          'message' => 'NWA - ' . $message->message,
          'timestamps' => ['+0 minutes'],
          'model_type' => $model_type,
          'model_id' =>  $model_id,
          'user_id' => '6',
          'sent_to' => '',
          'role' => 'message',
        ]);

        NotificationQueueController::createNewNotification([
          'message' => 'NWA - ' . $message->message,
          'timestamps' => ['+0 minutes'],
          'model_type' => $model_type,
          'model_id' =>  $model_id,
          'user_id' => '6',
          'sent_to' => '',
          'role' => 'Admin',
        ]);

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

            NotificationQueueController::createNewNotification([
  		        'message' => 'WAA - ' . $message->message,
  		        'timestamps' => ['+0 minutes'],
  		        'model_type' => $model_type,
  		        'model_id' =>  $model_id,
  		        'user_id' => Auth::id(),
  		        'sent_to' => '',
  		        'role' => 'message',
  	        ]);

            NotificationQueueController::createNewNotification([
      		    'message' => 'WAA - ' . $message->message,
      		    'timestamps' => ['+0 minutes'],
              'model_type' => $model_type,
  		        'model_id' =>  $model_id,
      		    'user_id' => Auth::id(),
      		    'sent_to' => '',
      		    'role' => 'Admin',
      	    ]);
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

            NotificationQueueController::createNewNotification([
              'message' => 'WAA - ' . $message->message,
              'timestamps' => ['+0 minutes'],
              'model_type' => $model_type,
              'model_id' =>  $model_id,
              'user_id' => Auth::id(),
              'sent_to' => '',
              'role' => 'message',
            ]);

            NotificationQueueController::createNewNotification([
              'message' => 'WAA - ' . $message->message,
              'timestamps' => ['+0 minutes'],
             'model_type' => $model_type,
              'model_id' =>  $model_id,
              'user_id' => Auth::id(),
              'sent_to' => '',
              'role' => 'Admin',
            ]);
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
        } else {
          $customer = Customer::find($message->customer_id);
          $params['customer_id'] = $customer->id;
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

          ChatMessage::create($params);
        }
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
            $messageParams['content_type'] = $headers["Content-Type"];
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
       // if ($context == "leads") {
       //      $id = $request->get("leadId");
       //      $model_type = 'leads';
       //      $params['lead_id'] = $id;
	     //    $messages = ChatMessage::where('lead_id', '=', $id);
       // } elseif ($context == "orders") {
       //      $id = $request->get("orderId");
       //      $model_type = 'order';
       //      $params['order_id'] = $id;
	     //    $messages = ChatMessage::where('order_id', '=', $id);
       //  }
        $messages = ChatMessage::where('customer_id', $request->customerId);
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
            $messageParams['content_type'] = $headers["Content-Type"];
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
       return response()->json($result);
    }

    public function approveMessage($context, Request $request)
	{
        $user = \Auth::user();
        $message = ChatMessage::findOrFail($request->get("messageId"));
        // $messages = ChatMessage::whereNull('message')->whereNotNull('media_url')->where('lead_id', $message->lead_id)
        //                         ->where('order_id', $message->order_id)->where('customer_id', $message->customer_id)->where('created_at', '>=', $message->created_at)->get();




        // if (count($messages) > 1) {
        //   $messages_keys = [];
        //   foreach ($messages as $message) {
        //     $send = $message->message;
        //     if (is_null($send)) {
        //         $send = $message->media_url;
        //
        //         if (is_null($send)) {
        //
        //         }
        //     }
        //     if ($context == "leads") {
        //         $lead = Leads::find($message->lead_id);
        //         $this->sendWithWhatsApp( $lead->contactno, $lead->whatsapp_number, $send);
        //     } elseif ( $context == "orders") {
        //         $order = Order::find($message->order_id);
        //         $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send);
        //     } elseif ($context == "customer") {
        //         $customer = Customer::find($message->customer_id);
        //
        //         if ($leads = $customer->leads) {
        //           foreach ($leads as $lead) {
        //             if ($lead->whatsapp_number) {
        //               $whatsapp_number = $lead->whatsapp_number;
        //             }
        //           }
        //         }
        //         if ($orders = $customer->orders) {
        //           foreach ($orders as $order) {
        //             if ($order->whatsapp_number) {
        //               $whatsapp_number = $order->whatsapp_number;
        //             }
        //           }
        //         }
        //
        //         $this->sendWithWhatsApp( $message->customer->phone,$whatsapp_number, $send);
        //     }
        //
        //     $message->update([
        //         'approved' => 1,
        //         'status'   => 2
        //     ]);
        //
        //     array_push($messages_keys, $message->id);
        //   }
        //
        //   return response($messages_keys);
        // } else {
          $send = $message->message;
          if (is_null($send)) {
              $send = $message->media_url;


              if (is_null($send)) {
                if ($images = $message->getMedia(config('constants.media_tags'))) {
                  foreach ($images as $image) {
                    $send = str_replace(' ', '%20', $image->getUrl());

                    if ($context == "leads") {
                        $lead = Leads::find($message->lead_id);
                        $this->sendWithWhatsApp( $lead->contactno, $lead->whatsapp_number, $send);
                    } elseif ( $context == "orders") {
                        $order = Order::find($message->order_id);
                        $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send);
                    } elseif ($context == "customer") {
                        $customer = Customer::find($message->customer_id);

                        // if ($leads = $customer->leads) {
                        //   foreach ($leads as $lead) {
                        //     if ($lead->whatsapp_number) {
                        //       $whatsapp_number = $lead->whatsapp_number;
                        //     }
                        //   }
                        // }
                        // if ($orders = $customer->orders) {
                        //   foreach ($orders as $order) {
                        //     if ($order->whatsapp_number) {
                        //       $whatsapp_number = $order->whatsapp_number;
                        //     }
                        //   }
                        // }

                        $this->sendWithWhatsApp( $message->customer->phone,$customer->whatsapp_number, $send);
                    }
                  }
                }
              } else {
                if ($context == "leads") {
                    $lead = Leads::find($message->lead_id);
                    $this->sendWithWhatsApp( $lead->contactno, $lead->whatsapp_number, $send);
                } elseif ( $context == "orders") {
                    $order = Order::find($message->order_id);
                    $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send);
                } elseif ($context == "customer") {
                    $customer = Customer::find($message->customer_id);

                    if ($leads = $customer->leads) {
                      foreach ($leads as $lead) {
                        if ($lead->whatsapp_number) {
                          $whatsapp_number = $lead->whatsapp_number;
                        }
                      }
                    }
                    if ($orders = $customer->orders) {
                      foreach ($orders as $order) {
                        if ($order->whatsapp_number) {
                          $whatsapp_number = $order->whatsapp_number;
                        }
                      }
                    }

                    $this->sendWithWhatsApp( $message->customer->phone,$whatsapp_number, $send);
                }
              }
          } else {
            if ($context == "leads") {
                $lead = Leads::find($message->lead_id);
                $this->sendWithWhatsApp( $lead->contactno, $lead->whatsapp_number, $send);
            } elseif ( $context == "orders") {
                $order = Order::find($message->order_id);
                $this->sendWithWhatsApp( $order->contact_detail,$order->whatsapp_number, $send);
            } elseif ($context == "customer") {
                $customer = Customer::find($message->customer_id);

                if ($leads = $customer->leads) {
                  foreach ($leads as $lead) {
                    if ($lead->whatsapp_number) {
                      $whatsapp_number = $lead->whatsapp_number;
                    }
                  }
                }
                if ($orders = $customer->orders) {
                  foreach ($orders as $order) {
                    if ($order->whatsapp_number) {
                      $whatsapp_number = $order->whatsapp_number;
                    }
                  }
                }

                $this->sendWithWhatsApp( $message->customer->phone,$whatsapp_number, $send);
            }
          }


          $message->update([
              'approved' => 1,
              'status'   => 2
          ]);
        // }

        return response("success");
    }

	private function sendWithWhatsApp($number, $sendNumber, $text)
	{
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
        //$number = "";
        $url = "https://panel.apiwha.com/send_message.php?apikey=".$key."&number=".$encodedNumber."&text=" . $encodedText;
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
        $moduleid = $request->get('moduleid');
        $moduletype = $request->get('moduletype');
        $message->save();

      if( $message->status == '5' ) {
		    NotificationQueueController::createNewNotification( [
			    'message'    => 'Message was read : ' . $message->message,
			    'timestamps' => [ '+0 minutes' ],
			    'model_type' => $moduletype,
			    'model_id'   => $moduleid,
			    'user_id'    => Auth::id(),
			    'sent_to'    => '',
			    'role'       => 'Admin',
		    ] );
	    }

      if( $message->status == '6' ) {
        if ($notifications = PushNotification::where('model_id', $moduleid)->where('model_type', $moduletype)->get()) {
          foreach ($notifications as $notification) {
            $notification->isread = 1;
            $notification->save();
          }
        }

        if ($notifications_queue = NotificationQueue::where('model_id', $moduleid)->where('model_type', $moduletype)->get()) {
          foreach ($notifications_queue as $notification) {
            $notification->delete();
          }
        }

		    NotificationQueueController::createNewNotification( [
			    'message'    => 'Message Sent : ' . $message->message,
			    'timestamps' => [ '+0 minutes' ],
			    'model_type' => $moduletype,
			    'model_id'   => $moduleid,
			    'user_id'    => Auth::id(),
			    'sent_to'    => '6',
			    'role'       => 'Admin',
		    ] );
	    }

	    // return redirect('/'. $moduletype.'/'.$moduleid);
    }
}
