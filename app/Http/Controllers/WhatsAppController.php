<?php

namespace App\Http\Controllers;

use App\Blogger;
use App\DeveloperTask;
use App\Issue;
use App\Lawyer;
use App\LegalCase;
use App\Services\BulkCustomerMessage\KeywordsChecker;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Bugsnag\PsrLogger\BugsnagLogger;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\URL;
use Plank\Mediable\MediaUploaderFacade;
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
use App\Vendor;
use App\Setting;
use App\Dubbizle;
use App\User;
use App\Brand;
use App\Product;
use App\Contact;
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
use App\Jobs\SendImagesWithWhatsapp;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Imports\CustomerNumberImport;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Illuminate\Support\Facades\DB;
use Validator;
use Image;
use GuzzleHttp\Client as GuzzleClient;
use File;
use App\Document;
use App\WhatsAppGroup;


class WhatsAppController extends FindByNumberController
{
    /**
     * Incoming message URL for whatsApp
     *
     * @return \Illuminate\Http\Response
     */
    public function incomingMessage(Request $request, GuzzleClient $client)
    {
        $data = $request->json()->all();

        if ($data[ 'event' ] == 'INBOX') {
            $to = $data[ 'to' ];
            $from = $data[ 'from' ];
            $text = $data[ 'text' ];
            $lead = $this->findLeadByNumber($from);
            $user = $this->findUserByNumber($from);
            $supplier = $this->findSupplierByNumber($from);
            $customer = $this->findCustomerByNumber($from);

            $params = [
                'number' => $from
            ];

            if ($user) {
                $params = $this->modifyParamsWithMessage($params, $data);
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

                $params[ 'erp_user' ] = $user->id;

                $params = $this->modifyParamsWithMessage($params, $data);

                if (array_key_exists('message', $params) && (preg_match_all("/#([\d]+)/i", $params[ 'message' ], $match))) {
                    $params[ 'task_id' ] = $match[ 1 ][ 0 ];
                }

                $message = ChatMessage::create($params);
                $model_type = 'user';
                $model_id = $user->id;

                if (array_key_exists('task_id', $params)) {
                    $this->sendRealTime($message, 'task_' . $match[ 1 ][ 0 ], $client);
                } else {
                    $this->sendRealTime($message, 'erp_user_' . $user->id, $client);
                }
            }

            if ($supplier) {
                $params[ 'erp_user' ] = null;
                $params[ 'task_id' ] = null;
                $params[ 'supplier_id' ] = $supplier->id;

                $params = $this->modifyParamsWithMessage($params, $data);
                $message = ChatMessage::create($params);
                $model_type = 'supplier';
                $model_id = $supplier->id;

                $this->sendRealTime($message, 'supplier_' . $supplier->id, $client);
            }

            if ($customer) {
                $params[ 'erp_user' ] = null;
                $params[ 'supplier_id' ] = null;
                $params[ 'task_id' ] = null;
                $params[ 'customer_id' ] = $customer->id;

                $params = $this->modifyParamsWithMessage($params, $data);
                $message = ChatMessage::create($params);

                if ($params['message']) {
                    (new KeywordsChecker())->assignCustomerAndKeywordForNewMessage($params['message'], $customer);
                }

                $model_type = 'customers';
                $model_id = $customer->id;
                $customer->update([
                    'whatsapp_number' => $to
                ]);

                $this->sendRealTime($message, 'customer_' . $customer->id, $client);

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

                                $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $forwarded_message, false, $message->id);

                                if ($second_message != '') {
                                    $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, false, $message->id);
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

                            $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $forwarded_message, false, $message->id);

                            if ($second_message != '') {
                                $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, false, $message->id);
                            }
                        }
                    }
                }

                // Auto DND
                if (array_key_exists('message', $params) && strtoupper($params[ 'message' ]) == 'DND') {
                    if ($customer = Customer::find($params[ 'customer_id' ])) {
                        $customer->do_not_disturb = 1;
                        $customer->save();

                        $dnd_params = [
                            'number' => null,
                            'user_id' => 6,
                            'approved' => 1,
                            'status' => 9,
                            'customer_id' => $customer->id,
                            'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'customer-dnd')->first()->reply
                        ];

                        $auto_dnd_message = ChatMessage::create($dnd_params);

                        $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $dnd_params[ 'message' ], false, $auto_dnd_message->id);
                    }
                }

                // Auto Instruction
                if (array_key_exists('message', $params) && (preg_match("/price/i", $params[ 'message' ]) || preg_match("/you photo/i", $params[ 'message' ]) || preg_match("/pp/i", $params[ 'message' ]) || preg_match("/how much/i", $params[ 'message' ]) || preg_match("/cost/i", $params[ 'message' ]) || preg_match("/rate/i", $params[ 'message' ]))) {
                    if ($customer = Customer::find($params[ 'customer_id' ])) {
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
                                            'rating' => 1,
                                            'status' => 3,
                                            'assigned_user' => 6,
                                            'selected_product' => json_encode($selected_products),
                                            'created_at' => Carbon::now()
                                        ]);

                                        $requestData = new Request();
                                        $requestData->setMethod('POST');
                                        $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                                        app('App\Http\Controllers\LeadsController')->sendPrices($requestData);

                                        CommunicationHistory::create([
                                            'model_id' => $latest_broadcast_message->id,
                                            'model_type' => ChatMessage::class,
                                            'type' => 'broadcast-prices',
                                            'method' => 'whatsapp'
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
                    if (array_key_exists('message', $params) && $params[ 'message' ] != '') {
                        $keyword = $auto_reply->keyword;

                        if (preg_match("/{$keyword}/i", $params[ 'message' ])) {
                            $temp_params = $params;
                            $temp_params[ 'message' ] = $auto_reply->reply;
                            $temp_params[ 'status' ] = 1;

                            ChatMessage::create($temp_params);

                            $this->sendRealTime($message, 'customer_' . $customer->id, $client);
                        }
                    }
                }
            }

            if (!isset($user) && !isset($purchase) && !isset($customer)) {
                $modal_type = 'leads';
                // $new_name = "whatsapp lead " . uniqid( TRUE );
                $user = User::get()[ 0 ];
                $validate_phone[ 'phone' ] = $from;

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

                    $params[ 'lead_id' ] = $lead->id;
                    $params[ 'customer_id' ] = $customer->id;
                    $params = $this->modifyParamsWithMessage($params, $data);
                    $message = ChatMessage::create($params);
                    $model_type = 'leads';
                    $model_id = $lead->id;

                    $this->sendRealTime($message, 'customer_' . $customer->id, $client);
                }
            }

            // Auto Respond
            $today_date = Carbon::now()->format('Y-m-d');
            $time = Carbon::now();
            $start_time = Setting::get('start_time');
            $start_time_exploded = explode(':', $start_time);
            $end_time = Setting::get('end_time');
            $end_time_exploded = explode(':', $end_time);
            $morning = Carbon::create($time->year, $time->month, $time->day, $start_time_exploded[ 0 ], $start_time_exploded[ 1 ], 0);
            $not_morning = Carbon::create($time->year, $time->month, $time->day, 0, 0, 0);
            $evening = Carbon::create($time->year, $time->month, $time->day, $end_time_exploded[ 0 ], $end_time_exploded[ 1 ], 0);
            $not_evening = Carbon::create($time->year, $time->month, $time->day, 23, 59, 0);
            $saturday = Carbon::now()->endOfWeek()->subDay()->format('Y-m-d');
            $sunday = Carbon::now()->endOfWeek()->format('Y-m-d');

            $chat_messages_query = ChatMessage::where('customer_id', $params[ 'customer_id' ])->whereBetween('created_at', [$morning, $evening])->whereNotNull('number');
            $chat_messages_count = $chat_messages_query->count();

            $chat_messages_evening_query = ChatMessage::where('customer_id', $params[ 'customer_id' ])->where(function ($query) use ($not_morning, $morning, $evening, $not_evening) {
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
                $customer = Customer::find($params[ 'customer_id' ]);
                $params = [
                    'number' => null,
                    'user_id' => 6,
                    'approved' => 1,
                    'status' => 9,
                    'customer_id' => $params[ 'customer_id' ]
                ];

                if ($time->between($morning, $evening, true)) {
                    $params[ 'message' ] = AutoReply::where('type', 'auto-reply')->where('keyword', 'work-hours-greeting')->first()->reply;

                    sleep(1);
                    $additional_message = ChatMessage::create($params);
                    $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, false, $additional_message->id);
                }
            } else {
                if (($chat_messages_evening_count == 1 && (isset($chat_messages_evening_query_first) && $chat_messages_evening_query_first->id == $message->id)) || ($chat_messages_count == 1 && ($saturday == $today_date || $sunday == $today_date))) {
                    $customer = Customer::find($params[ 'customer_id' ]);

                    $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'office-closed-message')->first();

                    $auto_message = preg_replace("/{start_time}/i", $start_time, $auto_reply->reply);
                    $auto_message = preg_replace("/{end_time}/i", $end_time, $auto_message);

                    $params = [
                        'number' => null,
                        'user_id' => 6,
                        'approved' => 1,
                        'status' => 9,
                        'customer_id' => $params[ 'customer_id' ],
                        'message' => $auto_message
                    ];

                    sleep(1);
                    $additional_message = ChatMessage::create($params);
                    $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, false, $additional_message->id);
                }
            }
        } else {
            $custom_data = json_decode($data[ 'custom_data' ], true);

            $chat_message = ChatMessage::find($custom_data[ 'chat_message_id' ]);

            if ($chat_message) {
                $chat_message->sent = 1;
                $chat_message->save();
            }
        }

        return response("");
    }

    public function sendRealTime($message, $model_id, $client, $customFile = null)
    {
        $realtime_params = [
            'realtime_id' => $model_id,
            'id' => $message->id,
            'number' => $message->number,
            'assigned_to' => $message->assigned_to ?? '',
            'created_at' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
            'approved' => $message->approved ?? 0,
            'status' => $message->status ?? 0,
            'user_id' => $message->user_id ?? 0,
            'erp_user' => $message->erp_user ?? 0,
            'sent' => $message->sent ?? 0,
            'resent' => $message->resent ?? 0,
            'error_status' => $message->error_status ?? 0,
        ];

        // attach custom image or file here if not want to send original
        $mediaUrl = ($customFile && !empty($customFile)) ? $customFile : $message->media_url;

        if ($mediaUrl) {

            $realtime_params[ 'media_url' ] = $mediaUrl;
            $headers = get_headers($mediaUrl, 1);
            $realtime_params[ 'content_type' ] = is_string($headers[ "Content-Type" ]) ? $headers[ "Content-Type" ] : $headers[ "Content-Type" ][ 1 ];

        }

        if ($message->message) {
            $realtime_params[ 'message' ] = $message->message;
        }

        $response = $client->post('https://sololuxury.co/deliver-message', [
            'form_params' => $realtime_params
        ]);

        return response('success', 200);
    }

    public function incomingMessageNew(Request $request, GuzzleClient $client)
    {
        $data = $request->json()->all();

        if ($data[ 'event' ] == 'message:in:new') {
            $to = str_replace('+', '', $data[ 'data' ][ 'toNumber' ]);
            $from = str_replace('+', '', $data[ 'data' ][ 'fromNumber' ]);
            $text = $data[ 'data' ][ 'body' ];
            $lead = $this->findLeadByNumber($from);
            $user = $this->findUserByNumber($from);
            $supplier = $this->findSupplierByNumber($from);
            $customer = $this->findCustomerByNumber($from);
            $dubbizle = $this->findDubbizleByNumber($from);

            $params = [
                'number' => $from,
                'message' => ''
            ];

            if ($data[ 'data' ][ 'type' ] == 'text') {
                $params[ 'message' ] = $text;
            } else {
                if ($data[ 'data' ][ 'type' ] == 'image') {
                    $image_data = $data[ 'data' ][ 'media' ][ 'preview' ][ 'image' ];
                    $image_path = public_path() . '/uploads/temp_image.png';
                    $img = Image::make(base64_decode($image_data))->encode('jpeg')->save($image_path);

                    $media = MediaUploader::fromSource($image_path)->upload();

                    File::delete('uploads/temp_image.png');
                }
            }

            if ($user) {
                $instruction = Instruction::where('assigned_to', $user->id)->latest()->first();

                if ($instruction) {
                    $myRequest = new Request();
                    $myRequest->setMethod('POST');
                    $myRequest->request->add(['remark' => $params[ 'message' ], 'id' => $instruction->id, 'module_type' => 'instruction', 'user_name' => "User from Whatsapp"]);

                    app('App\Http\Controllers\TaskModuleController')->addRemark($myRequest);
                }

                NotificationQueueController::createNewNotification([
                    'message' => $params[ 'message' ],
                    'timestamps' => ['+0 minutes'],
                    'model_type' => Instruction::class,
                    'model_id' => $instruction->id,
                    'user_id' => '6',
                    'sent_to' => $instruction->assigned_from,
                    'role' => '',
                ]);

                $params[ 'erp_user' ] = $user->id;

                if ($params[ 'message' ] != '' && (preg_match_all("/TASK ID ([\d]+)/i", $params[ 'message' ], $match))) {
                    $params[ 'task_id' ] = $match[ 1 ][ 0 ];
                }


                $params = $this->modifyParamsWithMessage($params, $data);
                $message = ChatMessage::create($params);
                $model_type = 'user';
                $model_id = $user->id;

                if (array_key_exists('task_id', $params)) {
                    $this->sendRealTime($message, 'task_' . $match[ 1 ][ 0 ], $client);
                } else {
                    $this->sendRealTime($message, 'erp_user_' . $user->id, $client);
                }

                // if ($user->id == 3) {
                //   file_put_contents(__DIR__."/response.txt", json_encode($data));
                //
                //   if (array_key_exists('quoted', $data['data'])) {
                //     $quoted_id = $data['data']['quoted']['wid'];
                //
                //     $configs = \Config::get("wassenger.api_keys");
                //     // $encodedNumber = "+" . $number;
                //     // $encodedText = $message;
                //     $wa_token = $configs[0]['key'];
                //     $wa_device = $configs[1]['device'];
                //
                //     // $array = [
                //     //   'phone' => $encodedNumber,
                //     //   'message' => (string) $encodedText,
                //     //   'reference' => (string) $chat_message_id,
                //     //   'device'  => "$wa_device",
                //     //   'enqueue' => "$enqueue",
                //     // ];
                //
                //     $curl = curl_init();
                //
                //     curl_setopt_array($curl, array(
                //       CURLOPT_URL => "https://api.wassenger.com/v1/io/$wa_device/messages/$quoted_id",
                //       CURLOPT_RETURNTRANSFER => true,
                //       CURLOPT_ENCODING => "",
                //       CURLOPT_MAXREDIRS => 10,
                //       CURLOPT_TIMEOUT => 30,
                //       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //       CURLOPT_CUSTOMREQUEST => "GET",
                //       // CURLOPT_POSTFIELDS => json_encode($array),
                //       CURLOPT_HTTPHEADER => array(
                //         // "content-type: application/json",
                //         "token: $wa_token"
                //       ),
                //     ));
                //
                //     $response = curl_exec($curl);
                //     $err = curl_error($curl);
                //     $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                //
                //     curl_close($curl);
                //
                //     file_put_contents(__DIR__."/wow.txt", json_encode($response));
                //
                //     if ($err) {
                //       throw new \Exception("cURL Error #:" . $err);
                //     } else {
                //       $result = json_decode($response, true);
                //
                //       if ($http_code != 201) {
                //         throw new \Exception("Something was wrong with message: " . $result['message']);
                //       }
                //     }
                //   }
                // }
            }

            if ($supplier) {
                $params[ 'erp_user' ] = null;
                $params[ 'task_id' ] = null;
                $params[ 'supplier_id' ] = $supplier->id;

                $message = ChatMessage::create($params);
                $model_type = 'supplier';
                $model_id = $supplier->id;

                $this->sendRealTime($message, 'supplier_' . $supplier->id, $client);
            }

            if ($dubbizle) {
                $params[ 'erp_user' ] = null;
                $params[ 'task_id' ] = null;
                $params[ 'supplier_id' ] = null;
                $params[ 'dubbizle_id' ] = $dubbizle->id;

                $message = ChatMessage::create($params);
                $model_type = 'dubbizle';
                $model_id = $dubbizle->id;

                $this->sendRealTime($message, 'dubbizle_' . $dubbizle->id, $client);
            }

            if ($customer) {
                $params[ 'erp_user' ] = null;
                $params[ 'supplier_id' ] = null;
                $params[ 'task_id' ] = null;
                $params[ 'dubbizle_id' ] = null;
                $params[ 'customer_id' ] = $customer->id;

                $message = ChatMessage::create($params);

                if ($params['message']) {
                    (new KeywordsChecker())->assignCustomerAndKeywordForNewMessage($params['message'], $customer);
                }

                $model_type = 'customers';
                $model_id = $customer->id;
                $customer->update([
                    'whatsapp_number' => $to
                ]);

                $this->sendRealTime($message, 'customer_' . $customer->id, $client);

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
                                $this->sendWithNewApi($user->phone, $user->whatsapp_number, $forwarded_message, null, $message->id);

                                if ($second_message != '') {
                                    // $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, FALSE, $message->id);
                                    $this->sendWithNewApi($user->phone, $user->whatsapp_number, null, $second_message, $message->id);
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
                            $this->sendWithNewApi($user->phone, $user->whatsapp_number, $forwarded_message, null, $message->id);

                            if ($second_message != '') {
                                // $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, FALSE, $message->id);
                                $this->sendWithNewApi($user->phone, $user->whatsapp_number, null, $second_message, $message->id);
                            }
                        }
                    }
                }

                // Auto DND
                if (array_key_exists('message', $params) && strtoupper($params[ 'message' ]) == 'DND') {
                    if ($customer = Customer::find($params[ 'customer_id' ])) {
                        $customer->do_not_disturb = 1;
                        $customer->save();

                        $dnd_params = [
                            'number' => null,
                            'user_id' => 6,
                            'approved' => 1,
                            'status' => 9,
                            'customer_id' => $customer->id,
                            'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'customer-dnd')->first()->reply
                        ];

                        $auto_dnd_message = ChatMessage::create($dnd_params);

                        // $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $dnd_params['message'], FALSE, $auto_dnd_message->id);
                        $this->sendWithNewApi($customer->phone, $customer->whatsapp_number, $dnd_params[ 'message' ], null, $auto_dnd_message->id);
                    }
                }

                // Auto Instruction
                if (array_key_exists('message', $params) && (preg_match("/price/i", $params[ 'message' ]) || preg_match("/you photo/i", $params[ 'message' ]) || preg_match("/pp/i", $params[ 'message' ]) || preg_match("/how much/i", $params[ 'message' ]) || preg_match("/cost/i", $params[ 'message' ]) || preg_match("/rate/i", $params[ 'message' ]))) {
                    if ($customer = Customer::find($params[ 'customer_id' ])) {
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
                                            'rating' => 1,
                                            'status' => 3,
                                            'assigned_user' => 6,
                                            'selected_product' => json_encode($selected_products),
                                            'created_at' => Carbon::now()
                                        ]);

                                        $requestData = new Request();
                                        $requestData->setMethod('POST');
                                        $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                                        app('App\Http\Controllers\LeadsController')->sendPrices($requestData);

                                        CommunicationHistory::create([
                                            'model_id' => $latest_broadcast_message->id,
                                            'model_type' => ChatMessage::class,
                                            'type' => 'broadcast-prices',
                                            'method' => 'whatsapp'
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
                    if (array_key_exists('message', $params) && $params[ 'message' ] != '') {
                        $keyword = $auto_reply->keyword;

                        if (preg_match("/{$keyword}/i", $params[ 'message' ])) {
                            $temp_params = $params;
                            $temp_params[ 'message' ] = $auto_reply->reply;
                            $temp_params[ 'status' ] = 1;

                            ChatMessage::create($temp_params);

                            $this->sendRealTime($message, 'customer_' . $customer->id, $client);
                        }
                    }
                }
            }

            if (!isset($user) && !isset($purchase) && !isset($customer)) {
                $modal_type = 'leads';
                // $new_name = "whatsapp lead " . uniqid( TRUE );
                $user = User::get()[ 0 ];
                $validate_phone[ 'phone' ] = $from;

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

                    $params[ 'lead_id' ] = $lead->id;
                    $params[ 'customer_id' ] = $customer->id;

                    $message = ChatMessage::create($params);
                    $model_type = 'leads';
                    $model_id = $lead->id;

                    $this->sendRealTime($message, 'customer_' . $customer->id, $client);
                }
            }

            // Auto Respond
            $today_date = Carbon::now()->format('Y-m-d');
            $time = Carbon::now();
            $start_time = Setting::get('start_time');
            $start_time_exploded = explode(':', $start_time);
            $end_time = Setting::get('end_time');
            $end_time_exploded = explode(':', $end_time);
            $morning = Carbon::create($time->year, $time->month, $time->day, $start_time_exploded[ 0 ], $start_time_exploded[ 1 ], 0);
            $not_morning = Carbon::create($time->year, $time->month, $time->day, 0, 0, 0);
            $evening = Carbon::create($time->year, $time->month, $time->day, $end_time_exploded[ 0 ], $end_time_exploded[ 1 ], 0);
            $not_evening = Carbon::create($time->year, $time->month, $time->day, 23, 59, 0);
            $saturday = Carbon::now()->endOfWeek()->subDay()->format('Y-m-d');
            $sunday = Carbon::now()->endOfWeek()->format('Y-m-d');

            $chat_messages_query = ChatMessage::where('customer_id', $params[ 'customer_id' ])->whereBetween('created_at', [$morning, $evening])->whereNotNull('number');
            $chat_messages_count = $chat_messages_query->count();

            $chat_messages_evening_query = ChatMessage::where('customer_id', $params[ 'customer_id' ])->where(function ($query) use ($not_morning, $morning, $evening, $not_evening) {
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
                $customer = Customer::find($params[ 'customer_id' ]);
                $params = [
                    'number' => null,
                    'user_id' => 6,
                    'approved' => 1,
                    'status' => 9,
                    'customer_id' => $params[ 'customer_id' ]
                ];

                if ($time->between($morning, $evening, true)) {
                    $params[ 'message' ] = AutoReply::where('type', 'auto-reply')->where('keyword', 'work-hours-greeting')->first()->reply;

                    sleep(1);
                    $additional_message = ChatMessage::create($params);
                    // $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, FALSE, $additional_message->id);
                    $this->sendWithNewApi($message->customer->phone, $customer->whatsapp_number, $additional_message->message, null, $additional_message->id);
                }
            } else {
                if (($chat_messages_evening_count == 1 && (isset($chat_messages_evening_query_first) && $chat_messages_evening_query_first->id == $message->id)) || ($chat_messages_count == 1 && ($saturday == $today_date || $sunday == $today_date))) {
                    $customer = Customer::find($params[ 'customer_id' ]);

                    $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'office-closed-message')->first();

                    $auto_message = preg_replace("/{start_time}/i", $start_time, $auto_reply->reply);
                    $auto_message = preg_replace("/{end_time}/i", $end_time, $auto_message);

                    $params = [
                        'number' => null,
                        'user_id' => 6,
                        'approved' => 1,
                        'status' => 9,
                        'customer_id' => $params[ 'customer_id' ],
                        'message' => $auto_message
                    ];

                    sleep(1);
                    $additional_message = ChatMessage::create($params);
                    // $this->sendWithWhatsApp($message->customer->phone, $customer->whatsapp_number, $additional_message->message, FALSE, $additional_message->id);
                    $this->sendWithNewApi($message->customer->phone, $customer->whatsapp_number, $additional_message->message, null, $additional_message->id);
                }
            }

            if ($data[ 'data' ][ 'type' ] == 'image') {
                $message->attachMedia($media, config('constants.media_tags'));
            }
        } else {
            $custom_data = json_decode($data[ 'custom_data' ], true);

            $chat_message = ChatMessage::find($custom_data[ 'chat_message_id' ]);

            if ($chat_message) {
                $chat_message->sent = 1;
                $chat_message->save();
            }
        }

        return response("success", 200);
    }

    public function webhook(Request $request, GuzzleClient $client)
    {
        $data = $request->json()->all();

        file_put_contents(__DIR__ . "/webhook.txt", json_encode($data));
        file_put_contents(__DIR__ . "/test_aayo.txt", json_encode($data), FILE_APPEND);

        // $to = str_replace('+', '', $data['data']['toNumber']);
        if (!array_key_exists('messages', $data)) {
            return response('ACK', 200);
        }

        $from = str_replace('@c.us', '', $data[ 'messages' ][ 0 ][ 'author' ]);
        $instanceId = $data[ 'instanceId' ];
        $text = $data[ 'messages' ][ 0 ][ 'body' ];
        $contentType = $data[ 'messages' ][ 0 ][ 'type' ];
        $originalMessage = $text;
        $supplier = $this->findSupplierByNumber($from);
        $vendor = $this->findVendorByNumber($from);
        $user = $this->findUserByNumber($from);
        $dubbizle = $this->findDubbizleByNumber($from);
        $contact = $this->findContactByNumber($from);
        $customer = $this->findCustomerByNumber($from);

        $params = [
            'number' => $from,
            'message' => '',
            'approved' => $data[ 'messages' ][ 0 ][ 'fromMe' ] ? 1 : 0,
            'status' => $data[ 'messages' ][ 0 ][ 'fromMe' ] ? 2 : 0
        ];

        if (filter_var($text, FILTER_VALIDATE_URL)) {
            $exploded = explode('//', $text);
            $http = array_shift($exploded);

            $formatted_url = $http . "//" . implode('/', $exploded);
//        $formatted_url = str_replace_last('.jpeg', '.jpg', $formatted_url);


            // $paths = explode("/", $text);
            // $file = $paths[count($paths) - 1];
            // $extension = explode(".", $file)[1];
            // $fileName = uniqid(TRUE).".".$extension;
            // $contents = file_get_contents($text);
            // if (file_put_contents(implode(DIRECTORY_SEPARATOR, array(\Config::get("apiwha.media_path"), $fileName)), $contents ) ==  FALSE) {
            //     return FALSE;
            // }
            // $url = implode("/", array( \Config::get("app.url"), "uploads", $fileName));
            $file_path = public_path() . '/uploads' . '/one.jpg';
            $params[ 'media_url' ] = '';

            try {
                copy($formatted_url, $file_path);

                $media = MediaUploader::fromSource($file_path)->useFilename(uniqid(true, true))->upload();

                unlink($file_path);

                $params[ 'media_url' ] = $media->getUrl();

            } catch (\Exception $exception) {
                file_put_contents(__DIR__ . "/test_aayo.txt", $exception->getMessage());
            }

        } else {
            $params[ 'message' ] = $text;
        }

        // if ($data['messages'][0]['fromMe'] == false) {
        // if ($data['data']['type'] == 'text') {

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

            // $params['erp_user'] = $user->id;
            $params[ 'user_id' ] = $user->id;

            if ($params[ 'message' ] != '' && (preg_match_all("/#([\d]+)/i", $params[ 'message' ], $match))) {
                if ($task = Task::find($match[ 1 ][ 0 ])) {
                    $params[ 'task_id' ] = $match[ 1 ][ 0 ];

                    if (count($task->users) > 0) {
                        if ($task->assign_from == $user->id) {
                            $params[ 'erp_user' ] = $task->assign_to;
                        } else {
                            $params[ 'erp_user' ] = $task->assign_from;
                        }
                    }

                    if (count($task->contacts) > 0) {
                        if ($task->assign_from == $user->id) {
                            $params[ 'contact_id' ] = $task->assign_to;
                        } else {
                            $params[ 'contact_id' ] = $task->assign_from;
                        }
                    }
                }
            }

            // $params = $this->modifyParamsWithMessage($params, $data);
            if (isset($media)) {
                $params[ 'media_url' ] = $media->getUrl();
            }

            $message = ChatMessage::create($params);

            if (isset($media)) {
                $message->attachMedia($media, config('constants.media_tags'));
            }

            if (array_key_exists('task_id', $params)) {
                $this->sendRealTime($message, 'task_' . $task->id, $client);
            } else {
                $this->sendRealTime($message, 'user_' . $user->id, $client);
            }
            // $model_type = 'user';
            // $model_id = $user->id;
        } else {
            if (!$supplier) {

            }
        }

        $contact = Contact::where('phone', $from)->first();

        if ($contact) {
            $params[ 'contact_id' ] = $contact->id;
            $params[ 'user_id' ] = null;

            if ($params[ 'message' ] != '' && (preg_match_all("/#([\d]+)/i", $params[ 'message' ], $match))) {
                $params[ 'task_id' ] = $match[ 1 ][ 0 ];
            }

            $message = ChatMessage::create($params);

            if (array_key_exists('task_id', $params)) {
                $this->sendRealTime($message, 'task_' . $match[ 1 ][ 0 ], $client);
            } else {
                $this->sendRealTime($message, 'user_' . $contact->id, $client);
            }
        }

        if ($supplier) {
            $params[ 'erp_user' ] = null;
            $params[ 'task_id' ] = null;
            $params[ 'contact_id' ] = null;
            $params[ 'user_id' ] = null;
            $params[ 'supplier_id' ] = $supplier->id;

            $message = ChatMessage::create($params);
            // $model_type = 'supplier';
            // $model_id = $supplier->id;

            $this->sendRealTime($message, 'supplier_' . $supplier->id, $client);
        }

        if ($vendor) {
            $params[ 'erp_user' ] = null;
            $params[ 'task_id' ] = null;
            $params[ 'contact_id' ] = null;
            $params[ 'user_id' ] = null;
            $params[ 'supplier_id' ] = null;
            $params[ 'vendor_id' ] = $vendor->id;

            $message = ChatMessage::create($params);

            $category = $vendor->category;

            if ($category && $category->user_id && $params[ 'message' ]) {
                $user = User::find($category->user_id);
                $this->sendWithThirdApi($user->phone, null, 'V-' . $vendor->id . '-(' . $vendor->name . ')=> ' . $params[ 'message' ]);
            }


            // $model_type = 'supplier';
            // $model_id = $supplier->id;

            $this->sendRealTime($message, 'vendor_' . $vendor->id, $client);
        }

        if ($dubbizle) {
            $params[ 'erp_user' ] = null;
            $params[ 'task_id' ] = null;
            $params[ 'supplier_id' ] = null;
            $params[ 'vendor' ] = null;
            $params[ 'contact_id' ] = null;
            $params[ 'user_id' ] = null;
            $params[ 'dubbizle_id' ] = $dubbizle->id;

            $message = ChatMessage::create($params);
            $model_type = 'dubbizle';
            $model_id = $dubbizle->id;

            $this->sendRealTime($message, 'dubbizle_' . $dubbizle->id, $client);
        }
        // }

        $fromMe = $data[ 'messages' ][ 0 ][ 'fromMe' ] ?? true;
        $params[ 'message' ] = $originalMessage;
        if (!$fromMe && $params[ 'message' ] && strpos($originalMessage, 'V-') === 0) {
            $msg = $params[ 'message' ];
            $msg = explode(' ', $msg);
            $vendorData = $msg[ 0 ];
            $vendorId = trim(str_replace('V-', '', $vendorData));
            $message = str_replace('V-' . $vendorId, '', $params[ 'message' ]);

            $vendor = Vendor::find($vendorId);
            if (!$vendor) {
                return response('success');
            }

            $params[ 'vendor_id' ] = $vendorId;
            $params[ 'approved' ] = 1;
            $params[ 'message' ] = $message;
            $params[ 'status' ] = 2;

            $this->sendWithThirdApi($vendor->phone, null, $params[ 'message' ]);

            ChatMessage::create($params);

        }

        if (!$fromMe && strpos($originalMessage, '#ISSUE-') === 0) {
            $m = new ChatMessage();
            $message = str_replace('#ISSUE-', '', $originalMessage);
            $m->issue_id = explode(' ', $message)[ 0 ];
            $m->message = $originalMessage;
            $m->save();
        }

        if (!$fromMe && strpos($originalMessage, '#DEVTASK-') === 0) {
            $m = new ChatMessage();
            $message = str_replace('#DEVTASK-', '', $originalMessage);
            $m->developer_task_id = explode(' ', $message)[ 0 ];
            $m->message = $originalMessage;
            $m->save();
        }

        if ($instanceId == '43281') { // Indian
            $to = "919004780634";
        } else {
            if ($instanceId == '55202') { // Solo 06
                $to = '919152731486';
            } else {
                if ($instanceId == '55211') { // Solo 04
                    $to = '919152731483';
                } else { // James
                    $to = "971545889192";
                }
            }
        }


        if ($customer && ($to == '919152731483' || $to == '919152731486')) {
            $params[ 'erp_user' ] = null;
            $params[ 'supplier_id' ] = null;
            $params[ 'task_id' ] = null;
            $params[ 'dubbizle_id' ] = null;
            $params[ 'customer_id' ] = $customer->id;

            $message = ChatMessage::create($params);

            if ($params['message']) {
                (new KeywordsChecker())->assignCustomerAndKeywordForNewMessage($params['message'], $customer);
            }

            if ($contentType === 'image') {
                $message->message = '';
                File::put(public_path('uploads') . '/downloaded.jpg', file_get_contents($params[ 'message' ]));
                $media = MediaUploaderFacade::fromSource(public_path('uploads') . '/downloaded.jpg')
                    ->useFilename(uniqid('whatsapp_', true))
                    ->upload();
                $message->attachMedia($media, 'gallery');
                $message->save();
            }

            $model_type = 'customers';
            $model_id = $customer->id;
            $customer->update([
                'whatsapp_number' => $to
            ]);

            $this->sendRealTime($message, 'customer_' . $customer->id, $client);

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
                            $this->sendWithThirdApi($user->phone, $user->whatsapp_number, $forwarded_message, null, $message->id);

                            if ($second_message != '') {
//                                 $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, FALSE, $message->id);
//                                $this->sendWithNewApi($user->phone, $user->whatsapp_number, NULL, $second_message, $message->id);
                                $this->sendWithThirdApi($user->phone, $user->whatsapp_number, null, $second_message, $message->id);
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
                        $this->sendWithThirdApi($user->phone, $user->whatsapp_number, $forwarded_message, null, $message->id);
//                        $this->sendWithNewApi($user->phone, $user->whatsapp_number, $forwarded_message, NULL, $message->id);

                        if ($second_message != '') {
                            // $this->sendWithWhatsApp($user->phone, $user->whatsapp_number, $second_message, FALSE, $message->id);
                            $this->sendWithThirdApi($user->phone, $user->whatsapp_number, null, $second_message, $message->id);
//                            $this->sendWithNewApi($user->phone, $user->whatsapp_number, NULL, $second_message, $message->id);
                        }
                    }
                }
            }

            // Auto DND
            if (array_key_exists('message', $params) && strtoupper($params[ 'message' ]) == 'DND') {
                if ($customer = Customer::find($params[ 'customer_id' ])) {
                    $customer->do_not_disturb = 1;
                    $customer->save();

                    $dnd_params = [
                        'number' => null,
                        'user_id' => 6,
                        'approved' => 1,
                        'status' => 9,
                        'customer_id' => $customer->id,
                        'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'customer-dnd')->first()->reply
                    ];

                    $auto_dnd_message = ChatMessage::create($dnd_params);

                    // $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $dnd_params['message'], FALSE, $auto_dnd_message->id);
//                    $this->sendWithNewApi($customer->phone, $customer->whatsapp_number, $dnd_params['message'], NULL, $auto_dnd_message->id);
                    $this->sendWithThirdApi($customer->phone, $customer->whatsapp_number, $dnd_params[ 'message' ], null, $auto_dnd_message->id);
                }
            }

            // Auto Instruction
            if ($params[ 'customer_id' ] != '1000' && $params[ 'customer_id' ] != '976' && array_key_exists('message', $params) && (preg_match("/price/i", $params[ 'message' ]) || preg_match("/you photo/i", $params[ 'message' ]) || preg_match("/pp/i", $params[ 'message' ]) || preg_match("/how much/i", $params[ 'message' ]) || preg_match("/cost/i", $params[ 'message' ]) || preg_match("/rate/i", $params[ 'message' ]))) {
                if ($customer = Customer::find($params[ 'customer_id' ])) {

                    // send price from meessage queue
                    $messageSentLast = \App\MessageQueue::where("customer_id",$customer->id)->where("sent",1)->orderBy("sending_time","desc")->first();
                    // if message found then start
                    $selected_products = [];
                    if($messageSentLast) {
                        $mqProducts = $messageSentLast->getImagesWithProducts();
                        if(!empty($mqProducts)) {
                            foreach($mqProducts as $mq) {
                                if(!empty($mq["products"])) {
                                    foreach($mq["products"] as $productId) {
                                       $selected_products[] = $productId;     
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($selected_products) && $messageSentLast) {
                        $quick_lead = Leads::create([
                            'customer_id' => $customer->id,
                            'rating' => 1,
                            'status' => 3,
                            'assigned_user' => 6,
                            'selected_product' => json_encode($selected_products),
                            'created_at' => Carbon::now()
                        ]);

                        $requestData = new Request();
                        $requestData->setMethod('POST');
                        $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                        app('App\Http\Controllers\LeadsController')->sendPrices($requestData, new GuzzleClient);

                        CommunicationHistory::create([
                            'model_id' => $messageSentLast->id,
                            'model_type' => \App\MessageQueue::class,
                            'type' => 'broadcast-prices',
                            'method' => 'whatsapp'
                        ]);
                    }

                    /*$two_hours = Carbon::now()->subHours(2);
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
                                        'rating' => 1,
                                        'status' => 3,
                                        'assigned_user' => 6,
                                        'selected_product' => json_encode($selected_products),
                                        'created_at' => Carbon::now()
                                    ]);

                                    $requestData = new Request();
                                    $requestData->setMethod('POST');
                                    $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                                    app('App\Http\Controllers\LeadsController')->sendPrices($requestData);

                                    CommunicationHistory::create([
                                        'model_id' => $latest_broadcast_message->id,
                                        'model_type' => ChatMessage::class,
                                        'type' => 'broadcast-prices',
                                        'method' => 'whatsapp'
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
                    }*/

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
                if (array_key_exists('message', $params) && $params[ 'message' ] != '') {
                    $keyword = $auto_reply->keyword;

                    if (preg_match("/{$keyword}/i", $params[ 'message' ])) {
                        $temp_params = $params;
                        $temp_params[ 'message' ] = $auto_reply->reply;
                        $temp_params[ 'status' ] = 1;

                        ChatMessage::create($temp_params);

                        $this->sendRealTime($message, 'customer_' . $customer->id, $client);
                    }
                }
            }
        }


        return response('success');
    }

    public function outgoingProcessed(Request $request)
    {
        $data = $request->json()->all();

        // file_put_contents(__DIR__."/outgoing.txt", json_encode($data));

        foreach ($data as $event) {
            $chat_message = ChatMessage::find($event->data->reference);

            if ($chat_message) {
                $chat_message->sent = 1;
                $chat_message->save();
            }
        }

        return response("success", 200);
    }

    public function getAllMessages(Request $request)
    {
        // dd('asd');
        $params = [];
        $result = [];
        // $skip = $request->page && $request->page > 1 ? $request->page * 10 : 0;

        // $messages = ChatMessage::select(['id', 'customer_id', 'number', 'user_id', 'assigned_to', 'approved', 'status', 'sent', 'created_at', 'media_url', 'message'])->where('customer_id', $request->customerId)->latest();
        if ($request->customerId) {
            $column = 'customer_id';
            $value = $request->customerId;
        } else {
            if ($request->supplierId) {
                $column = 'supplier_id';
                $value = $request->supplierId;
            } else {
                if ($request->taskId) {
                    $column = 'task_id';
                    $value = $request->taskId;
                } else {
                    if ($request->erpUser) {
                        $column = 'erp_user';
                        $value = $request->erpUser;
                    } else {
                        if ($request->dubbizleId) {
                            $column = 'dubbizle_id';
                            $value = $request->dubbizleId;
                        } else {
                            $column = 'customer_id';
                            $value = $request->customerId;
                        }
                    }
                }
            }
        }


//      $messages = ChatMessage::select(['id', 'customer_id', 'number', 'user_id', 'erp_user', 'assigned_to', 'approved', 'status', 'sent', 'error_status', 'resent', 'created_at', 'media_url', 'message'])->where($column, $value)->where('status', '!=', 7);
        $messages = DB::select('
                  SELECT chat_messages.id, chat_messages.customer_id, chat_messages.number, chat_messages.user_id, chat_messages.erp_user, chat_messages.assigned_to, chat_messages.approved, chat_messages.status, chat_messages.sent, chat_messages.error_status, chat_messages.resent, chat_messages.created_at, chat_messages.media_url, chat_messages.message,
                  media.filename,
                  mediable_id

                  FROM chat_messages

                  LEFT JOIN (
                    SELECT * FROM media

                    RIGHT JOIN
                    (SELECT * FROM mediables WHERE mediable_type LIKE "%ChatMessage%") as mediables
                    ON mediables.media_id = media.id
                  ) AS media

                  ON mediable_id = chat_messages.id

                  WHERE ' . $column . ' = ' . $value . ' AND status != 7
                  ORDER BY chat_messages.created_at DESC
      ');

        dd($messages);

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
            $elapse = (int)$request->get("elapse");
            $date = new \DateTime;
            $date->modify(sprintf("-%s seconds", $elapse));
            // $messages = $messages->where('created_at', '>=', $date->format('Y-m-d H:i:s'));
        }

        foreach ($messages->latest()->get() as $message) {
            $messageParams = [
                'id' => $message->id,
                'number' => $message->number,
                'assigned_to' => $message->assigned_to,
                'created_at' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
                'approved' => $message->approved,
                'status' => $message->status,
                'user_id' => $message->user_id,
                'erp_user' => $message->erp_user,
                'sent' => $message->sent,
                'resent' => $message->resent,
                'error_status' => $message->error_status
            ];

            if ($message->media_url) {
                $messageParams[ 'media_url' ] = $message->media_url;
                $headers = get_headers($message->media_url, 1);
                $messageParams[ 'content_type' ] = $headers[ "Content-Type" ][ 1 ];
            }

            if ($message->message) {
                $messageParams[ 'message' ] = $message->message;
            }

            if ($message->hasMedia(config('constants.media_tags'))) {
                $images_array = [];

                // $images_raw = DB::select('
                //               SELECT * FROM media
                //
                //               RIGHT JOIN
                //               (SELECT * FROM mediables WHERE mediable_type LIKE "%ChatMessage%") as mediables
                //               ON
                // ');

                foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
                    dd($image);
                    $temp_image = [
                        'key' => $image->getKey(),
                        'image' => $image->getUrl(),
                        'product_id' => '',
                        'special_price' => '',
                        'size' => ''
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
                        $temp_image[ 'product_id' ] = $product_image->id;
                        $temp_image[ 'special_price' ] = $product_image->price_special;

                        $string = $product_image->supplier;
                        $expr = '/(?<=\s|^)[a-z]/i';
                        preg_match_all($expr, $string, $matches);
                        $supplier_initials = implode('', $matches[ 0 ]);
                        $temp_image[ 'supplier_initials' ] = strtoupper($supplier_initials);

                        if ($product_image->size != null) {
                            $temp_image[ 'size' ] = $product_image->size;
                        } else {
                            $temp_image[ 'size' ] = (string)$product_image->lmeasurement . ', ' . (string)$product_image->hmeasurement . ', ' . (string)$product_image->dmeasurement;
                        }
                    }

                    array_push($images_array, $temp_image);
                }

                $messageParams[ 'images' ] = $images_array;
            }

            $result[] = array_merge($params, $messageParams);
        }

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
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        return response()->json($result);
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


        //     $chat_messages = DB::table('chat_messages')->where('customer_id', $request->customerId);
        //
        //     $chat_messages = $chat_messages->join(DB::raw('(SELECT media_id, mediable_type, mediable_id FROM `mediables` WHERE mediable_type = "App\ChatMessage") as mediables LEFT JOIN (SELECT media_id FROM `mediables`) as product_mediables ON mediables.media_id = product_mediables.media_id'), 'chat_messages.id', '=', 'mediables.mediable_id', 'LEFT');
        //
        //     $messages = DB::table('messages')->where('customer_id', $request->customerId);
        //
        //     $messages = $messages->join(DB::raw('(SELECT media_id, mediable_type, mediable_id FROM `mediables` WHERE mediable_type = "App\Message") as mediables'), 'messages.id', '=', 'mediables.mediable_id', 'LEFT');
        //
        //
        //     $product_image = Product::with('Media')->whereHas('Media', function($q) {
        //        $q->where('media.id', '57075');
        //     })->select(['id', 'price_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->get();
        //
        //     dD($product_image->toSql());
        //
        //     foreach ($chat_messages->get() as $chat_message) {
        //       dump($chat_message->product_mediables);
        //     }
        //     dd('stap');
        //
        //
        //
        //
        //
        // $data = DB::select(DB::raw($sql));
        // $messages = [];
        //
        // foreach ($data as $datum) {
        //   dd($datum);
        //     $images = Media::whereIn('id', explode(',', $datum->media_ids))->get(['disk', 'filename', 'extension'])->toArray();
        //     $images = array_map(function($item) {
        //         return URL::to('/') . '/' . $item['disk'] . '/' . $item['filename'] . '.' . $item['extension'];
        //     }, $images);
        //     $message = [
        //         'id' => $datum->id,
        //         'message' => $datum->message,
        //         'images' => $images
        //     ];
        //     $messages[] = $message;
        // }
        //
        // return $messages;

    }


    /**
     * Send message
     *
     * @param Request $request
     * @param $context
     * @return \Illuminate\Http\Response
     * @throws \Plank\Mediable\Exceptions\MediaUrlException
     */
    public function sendMessage(Request $request, $context)
    {
        $this->validate($request, [
            // 'message'         => 'nullable|required_without:image,images,screenshot_path|string',
//        'image'           => 'nullable|required_without:message',
//        'screenshot_path' => 'nullable|required_without:message',
            'customer_id' => 'sometimes|nullable|numeric',
            'supplier_id' => 'sometimes|nullable|numeric',
            'task_id' => 'sometimes|nullable|numeric',
            'erp_user' => 'sometimes|nullable|numeric',
            'status' => 'required|numeric',
            'assigned_to' => 'sometimes|nullable',
            'lawyer_id' => 'sometimes|nullable|numeric',
            'case_id' => 'sometimes|nullable|numeric',
            'blogger_id' => 'sometimes|nullable|numeric',
        ]);

        $data = $request->except('_token');
        $data[ 'user_id' ] = !empty($request['user_id']) && (int) $request['user_id'] > 0 ? (int) $request['user_id'] : Auth::id();
        $data[ 'number' ] = $request['number'];
        // $params['status'] = 1;

        if ($context == 'customer') {
            $data[ 'customer_id' ] = $request->customer_id;
            $module_id = $request->customer_id;
        } elseif ($context == "purchase") {
            $data[ 'purchase_id' ] = $request->purchase_id;
            $module_id = $request->purchase_id;
        } elseif ($context == 'supplier') {
            $data[ 'supplier_id' ] = $request->supplier_id;
            $module_id = $request->supplier_id;
        } else {
            if ($context == 'vendor') {
                $data[ 'vendor_id' ] = $request->vendor_id;
                $module_id = $request->vendor_id;
                if ($request->get('is_vendor_user') == 'yes') {
                    $user = User::find($request->get('vendor_id'));
                    $vendor = Vendor::where('phone', $user->phone)->first();
                    $data[ 'vendor_id' ] = $vendor->id;
                    $module_id = $vendor->id;
                }
            } elseif ($context == 'task') {
                $data[ 'task_id' ] = $request->task_id;
                $task = Task::find($request->task_id);

                if ($task->is_statutory != 1) {
                    $data[ 'message' ] = "#" . $data[ 'task_id' ] . ". " . $task->task_subject . ". " . $data[ 'message' ];
                } else {
                    $data[ 'message' ] = $task->task_subject . ". " . $data[ 'message' ];
                }

                if (count($task->users) > 0) {
                    if ($task->assign_from == Auth::id()) {
                        foreach ($task->users as $key => $user) {
                            if ($key == 0) {
                                $data[ 'erp_user' ] = $user->id;
                            } else {
                                $this->sendWithThirdApi($user->phone, $user->whatsapp_number, $data[ 'message' ]);
                            }
                        }
                    } else {
                        if (!$task->users->contains(Auth::id())) {
                            $data[ 'erp_user' ] = $task->assign_from;

                            foreach ($task->users as $key => $user) {
                                $this->sendWithThirdApi($user->phone, $user->whatsapp_number, $data[ 'message' ]);
                            }
                        } else {
                            foreach ($task->users as $key => $user) {
                                if ($key == 0) {
                                    $data[ 'erp_user' ] = $task->assign_from;
                                } else {
                                    if ($user->id != Auth::id()) {
                                        $this->sendWithThirdApi($user->phone, $user->whatsapp_number, $data[ 'message' ]);
                                    }
                                }
                            }
                        }
                    }
                }

                if (count($task->contacts) > 0) {
                    // if ($task->assign_from == Auth::id()) {
                    foreach ($task->contacts as $key => $contact) {
                        if ($key == 0) {
                            $data[ 'contact_id' ] = $task->assign_to;
                        } else {
                            $this->sendWithThirdApi($contact->phone, null, $data[ 'message' ]);
                        }
                    }
                    // } else {
                    // $data['contact_id'] = $task->assign_from;
                    // }
                }

                $params[ 'approved' ] = 1;
                $params[ 'status' ] = 2;
                $chat_message = ChatMessage::create($data);

                $module_id = $request->task_id;

                // if ($data['erp_user'] != Auth::id()) {
                //   $data['status'] = 0;
                // }
            } elseif ($context == 'user') {
                $data[ 'erp_user' ] = $request->user_id;
                $module_id = $request->user_id;
                $user = User::find($request->user_id);
                $this->sendWithThirdApi($user->phone, $user->whatsapp_number, null, null);
            } elseif ($context == 'dubbizle') {
                $data[ 'dubbizle_id' ] = $request->dubbizle_id;
                $module_id = $request->dubbizle_id;
            } elseif ($context == 'issue') {
                $params[ 'issue_id' ] = $request->get('issue_id');
                $issue = Issue::find($request->get('issue_id'));
                $params[ 'erp_user' ] = $issue->user_id;
                $params[ 'approved' ] = 1;
                $params[ 'message' ] = '#ISSUE-' . $issue->id . '-' . $issue->subject . '=>' . $request->get('message');
                $params[ 'status' ] = 2;


                $number = User::find($issue->user_id);

                if (!$number) {
                    return response()->json(['message' => null]);
                }

                $number = $number->phone;

                $this->sendWithThirdApi($number, null, $params[ 'message' ]);


                $chat_message = ChatMessage::create($params);

                return response()->json(['message' => $chat_message]);

            } else {
                if ($context == 'developer_task') {
                    $params[ 'developer_task_id' ] = $request->get('developer_task_id');
                    $task = DeveloperTask::find($request->get('developer_task_id'));
                    $params[ 'erp_user' ] = $task->user_id;
                    $params[ 'approved' ] = 1;
                    $params[ 'message' ] = '#DEVTASK-' . $task->id . ' ' . $request->get('message');
                    $params[ 'status' ] = 2;

                    $number = User::find($task->user_id)->phone;

                    $this->sendWithThirdApi($number, null, $params[ 'message' ]);

                    $chat_message = ChatMessage::create($params);

                    return response()->json(['message' => $chat_message]);
                } else {
                    if ($context == 'lawyer') {
                        $data[ 'lawyer_id' ] = $request->lawyer_id;
                        $module_id = $request->lawyer_id;
                    } else {
                        if ($context == 'case') {
                            $data[ 'case_id' ] = $request->case_id;
                            $data[ 'lawyer_id' ] = $request->lawyer_id;
                            $module_id = $request->case_id;
                        } else {
                            if ($context == 'blogger') {
                                $data[ 'blogger_id' ] = $request->blogger_id;
                                $module_id = $request->blogger_id;
                            }
                        }
                    }
                }
            }
        }

        if ($context != 'task') {
            $params[ 'approved' ] = 0;
            $params[ 'status' ] = 1;
            $chat_message = ChatMessage::create($data);
        }

        // $data['status'] = 1;

        // if ($context == 'task' && $data['erp_user'] != Auth::id()) {
        //   $data['erp_user'] = Auth::id();
        //
        //   $another_message = ChatMessage::create($data);
        // }

        if ($request->hasFile('image')) {
            $media = MediaUploader::fromSource($request->file('image'))->upload();
            $chat_message->attachMedia($media, config('constants.media_tags'));

            // if ($context == 'task' && $data['erp_user'] != Auth::id()) {
            //   $another_message->attachMedia($media,config('constants.media_tags'));
            // }

            if ($context == 'task') {
                if (count($task->users) > 0) {
                    if ($task->assign_from == Auth::id()) {
                        foreach ($task->users as $key => $user) {
                            if ($key == 0) {
                                $data[ 'erp_user' ] = $user->id;
                            } else {
                                $this->sendWithThirdApi($user->phone, $user->whatsapp_number, null, $media->getUrl());
                            }
                        }
                    } else {
                        foreach ($task->users as $key => $user) {
                            if ($key == 0) {
                                $data[ 'erp_user' ] = $task->assign_from;
                            } else {
                                if ($user->id != Auth::id()) {
                                    $this->sendWithThirdApi($user->phone, $user->whatsapp_number, null, $media->getUrl());
                                }
                            }
                        }
                    }
                }

                if (count($task->contacts) > 0) {
                    foreach ($task->contacts as $key => $contact) {
                        if ($key == 0) {
                            $data[ 'contact_id' ] = $task->assign_to;
                        } else {
                            $this->sendWithThirdApi($contact->phone, null, null, $media->getUrl());
                        }
                    }
                }
            }
        }

        if ($request->images) {
            $imagesDecoded = json_decode($request->images);
            if (count($imagesDecoded) >= 10) {

                $temp_chat_message = ChatMessage::create($data);
                foreach ($imagesDecoded as $image) {
                    $media = Media::find($image);
                    $temp_chat_message->attachMedia($media, config('constants.media_tags'));
                }


                $fn = '';
                if ($context == 'customer') {
                    $fn = '_product';
                }
                $medias = Media::whereIn('id', $imagesDecoded)->get();
                $pdfView = view('pdf_views.images' . $fn, compact('medias'));
                $pdf = new Dompdf();
                $pdf->loadHtml($pdfView);
                $fileName = public_path() . '/' . uniqid('sololuxury_', true) . '.pdf';
                $pdf->render();
                File::put($fileName, $pdf->output());
                $media = MediaUploader::fromSource($fileName)->upload();
                $chat_message->attachMedia($media, 'gallery');
            } else {
                foreach (array_unique($imagesDecoded) as $image) {
                    $media = Media::find($image);
                    $chat_message->attachMedia($media, config('constants.media_tags'));
                }
            }

        }

        if ($request->screenshot_path != '') {
            $image_path = public_path() . '/uploads/temp_screenshot.png';
            $img = substr($request->screenshot_path, strpos($request->screenshot_path, ",") + 1);
            $img = Image::make(base64_decode($img))->encode('png')->save($image_path);

            $media = MediaUploader::fromSource($image_path)->upload();
            $chat_message->attachMedia($media, config('constants.media_tags'));

            // if ($context == 'task' && $data['erp_user'] != Auth::id()) {
            //   $another_message->attachMedia($media,config('constants.media_tags'));
            // }

            File::delete('uploads/temp_screenshot.png');
        }

        if ((Auth::id() == 6 || Auth::id() == 56 || Auth::id() == 3 || Auth::id() == 65 || $context == 'task' || $request->get('is_vendor_user') == 'yes') && $chat_message->status != 0) {
            $myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $chat_message->id]);
            $this->approveMessage($context, $myRequest);
        }

        if ($request->ajax()) {
            return response()->json(['message' => $chat_message]);
        }

        return redirect('/' . $context . '/' . $module_id);
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
                        'number' => null,
                        'message' => $request->message,
                        'user_id' => Auth::id()
                    ];

                    if ($lead->customer) {
                        $params[ 'customer_id' ] = $lead->customer->id;
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
            'number' => null,
            'status' => 1,
            'user_id' => Auth::id(),
        ];

        if ($message) {
            $params = [
                'approved' => 1,
                'status' => 2,
                'created_at' => Carbon::now()
            ];

            if ($request->moduletype == 'leads') {
                $params[ 'lead_id' ] = $message->moduleid;
                if ($lead = Leads::find($message->moduleid)) {
                    if ($lead->customer) {
                        $params[ 'customer_id' ] = $lead->customer->id;
                    }
                }
            } elseif ($request->moduletype == 'orders') {
                $params[ 'order_id' ] = $message->moduleid;
                if ($order = Order::find($message->moduleid)) {
                    if ($order->customer) {
                        $params[ 'customer_id' ] = $order->customer->id;
                    }
                }
            } elseif ($request->moduletype == 'customer') {
                $customer = Customer::find($message->customer_id);
                $params[ 'customer_id' ] = $customer->id;
            } elseif ($request->moduletype == 'purchase') {
                $params[ 'purchase_id' ] = $message->moduleid;
            }

            $images = $message->getMedia(config('constants.media_tags'));

            if ($images->first()) {
                $params[ 'message' ] = null;
                $chat_message = ChatMessage::create($params);

                foreach ($images as $img) {
                    $chat_message->attachMedia($img, config('constants.media_tags'));
                }
            } else {
                $params[ 'message' ] = $message->body;

                $chat_message = ChatMessage::create($params);
            }

            $myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $chat_message->id]);

            $result = $this->approveMessage($request->moduletype, $myRequest);

        } else {
            if ($request->moduletype == 'customer') {
                $params[ 'customer_id' ] = $request->moduleid;
                $params[ 'order_id' ] = null;
            } elseif ($request->moduletype == 'leads') {
                $params[ 'lead_id' ] = $request->moduleid;
                if ($lead = Leads::find($request->moduleid)) {
                    if ($lead->customer) {
                        $params[ 'customer_id' ] = $lead->customer->id;
                    }
                }
            } else {
                $params[ 'order_id' ] = $request->moduleid;
                if ($order = Order::find($request->moduleid)) {
                    if ($order->customer) {
                        $params[ 'customer_id' ] = $order->customer->id;
                    }
                }
            }

            if ($request->images) {
                $params[ 'message' ] = null;
                $chat_message = ChatMessage::create($params);
                foreach (json_decode($request->images) as $image) {
                    // $product = Product::find($product_id);
                    // $media = $product->getMedia(config('constants.media_tags'))->first();
                    // $params['media_url'] = $media->getUrl();
                    $media = Media::find($image);
                    $chat_message->attachMedia($media, config('constants.media_tags'));
                }
            }

            return redirect('/' . $request->moduletype . '/' . $request->moduleid);
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
                    $new_message->attachMedia($image, config('constants.media_tags'));
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

        $messages = ChatMessage::select(['id', "$column", 'number', 'user_id', 'assigned_to', 'approved', 'status', 'sent', 'resent', 'created_at', 'media_url', 'message'])->where($column, $column_value)->latest();
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
            $elapse = (int)$request->get("elapse");
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
                'status' => $message->status,
                'user_id' => $message->user_id,
                'sent' => $message->sent,
                'resent' => $message->resent,
            ];

            if ($message->media_url) {
                $messageParams[ 'media_url' ] = $message->media_url;
                $headers = get_headers($message->media_url, 1);
                $messageParams[ 'content_type' ] = $headers[ "Content-Type" ][ 1 ];
            }

            if ($message->message) {
                $messageParams[ 'message' ] = $message->message;
            }

            if ($message->hasMedia(config('constants.media_tags'))) {
                $images_array = [];

                foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
                    $temp_image = [
                        'key' => $image->getKey(),
                        'image' => $image->getUrl(),
                        'product_id' => '',
                        'special_price' => '',
                        'size' => ''
                    ];

                    $image_key = $image->getKey();

                    $product_image = Product::with('Media')
                        ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key)")
                        // ->whereHas('Media', function($q) use($image) {
                        //    $q->where('media.id', $image->getKey());
                        // })
                        ->select(['id', 'price_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

                    if ($product_image) {
                        $temp_image[ 'product_id' ] = $product_image->id;
                        $temp_image[ 'special_price' ] = $product_image->price_special;

                        $string = $product_image->supplier;
                        $expr = '/(?<=\s|^)[a-z]/i';
                        preg_match_all($expr, $string, $matches);
                        $supplier_initials = implode('', $matches[ 0 ]);
                        $temp_image[ 'supplier_initials' ] = strtoupper($supplier_initials);

                        if ($product_image->size != null) {
                            $temp_image[ 'size' ] = $product_image->size;
                        } else {
                            $temp_image[ 'size' ] = (string)$product_image->lmeasurement . ', ' . (string)$product_image->hmeasurement . ', ' . (string)$product_image->dmeasurement;
                        }
                    }

                    array_push($images_array, $temp_image);
                }

                $messageParams[ 'images' ] = $images_array;
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
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);
        return response()->json($result);
    }

    public function pollMessagesCustomer(Request $request)
    {
        $params = [];
        $result = [];
        // $skip = $request->page && $request->page > 1 ? $request->page * 10 : 0;

        // $messages = ChatMessage::select(['id', 'customer_id', 'number', 'user_id', 'assigned_to', 'approved', 'status', 'sent', 'created_at', 'media_url', 'message'])->where('customer_id', $request->customerId)->latest();
        if ($request->customerId) {
            $column = 'customer_id';
            $value = $request->customerId;
        } else {
            if ($request->supplierId) {
                $column = 'supplier_id';
                $value = $request->supplierId;
            } else {
                if ($request->vendorId) {
                    $column = 'vendor_id';
                    $value = $request->vendorId;
                } else {
                    if ($request->taskId) {
                        $column = 'task_id';
                        $value = $request->taskId;
                    } else {
                        if ($request->erpUser) {
                            $column = 'erp_user';
                            $value = $request->erpUser;
                        } else {
                            if ($request->dubbizleId) {
                                $column = 'dubbizle_id';
                                $value = $request->dubbizleId;
                            } else {
                                if ($request->lawyerId) {
                                    $column = 'lawyer_id';
                                    $value = $request->lawyerId;
                                } else {
                                    if ($request->caseId) {
                                        $column = 'case_id';
                                        $value = $request->caseId;
                                    } else {
                                        if ($request->bloggerId) {
                                            $column = 'blogger_id';
                                            $value = $request->bloggerId;
                                        } else {
                                            $column = 'customer_id';
                                            $value = $request->customerId;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $select_fields = ['id', 'customer_id', 'number', 'user_id', 'erp_user', 'assigned_to', 'approved', 'status', 'sent', 'error_status', 'resent', 'created_at', 'media_url', 'message'];
        if ($request->caseId) {
            array_push($select_fields, 'lawyer_id');
        }
        $messages = ChatMessage::select($select_fields)->where($column, $value)->where('status', '!=', 7);

        if ($request->caseId) {
            $messages = $messages->with('lawyer:id,name');
        }

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
            $elapse = (int)$request->get("elapse");
            $date = new \DateTime;
            $date->modify(sprintf("-%s seconds", $elapse));
            // $messages = $messages->where('created_at', '>=', $date->format('Y-m-d H:i:s'));
        }

        foreach ($messages->latest()->get() as $message) {
            $messageParams = [
                'id' => $message->id,
                'number' => $message->number,
                'assigned_to' => $message->assigned_to,
                'created_at' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
                'approved' => $message->approved,
                'status' => $message->status,
                'user_id' => $message->user_id,
                'erp_user' => $message->erp_user,
                'sent' => $message->sent,
                'resent' => $message->resent,
                'error_status' => $message->error_status
            ];
            if ($request->caseId) {
                $messageParams[ 'lawyer' ] = optional($message->lawyer)->name;
            }

            if ($message->media_url) {
                $messageParams[ 'media_url' ] = $message->media_url;
                $headers = get_headers($message->media_url, 1);
                $messageParams[ 'content_type' ] = $headers[ "Content-Type" ][ 1 ];
            }

            if ($message->message) {
                $messageParams[ 'message' ] = $message->message;
            }

            if ($message->hasMedia(config('constants.media_tags'))) {
                $images_array = [];

                foreach ($message->getMedia(config('constants.media_tags')) as $key => $image) {
                    $temp_image = [
                        'key' => $image->getKey(),
                        'image' => $image->getUrl(),
                        'product_id' => '',
                        'special_price' => '',
                        'size' => ''
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
                        $temp_image[ 'product_id' ] = $product_image->id;
                        $temp_image[ 'special_price' ] = $product_image->price_special;

                        $string = $product_image->supplier;
                        $expr = '/(?<=\s|^)[a-z]/i';
                        preg_match_all($expr, $string, $matches);
                        $supplier_initials = implode('', $matches[ 0 ]);
                        $temp_image[ 'supplier_initials' ] = strtoupper($supplier_initials);

                        if ($product_image->size != null) {
                            $temp_image[ 'size' ] = $product_image->size;
                        } else {
                            $temp_image[ 'size' ] = (string)$product_image->lmeasurement . ', ' . (string)$product_image->hmeasurement . ', ' . (string)$product_image->dmeasurement;
                        }
                    }

                    array_push($images_array, $temp_image);
                }

                $messageParams[ 'images' ] = $images_array;
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
        $perPage = 10000;

        if ($request->page) {
            $currentItems = array_slice($result, $perPage * ($currentPage - 1), $perPage);

        } else {
            $currentItems = array_reverse(array_slice($result, $perPage * ($currentPage - 1), $perPage));
            $result = array_reverse($result);
        }

        $result = new LengthAwarePaginator($currentItems, count($result), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        return response()->json($result);
    }

    public function approveMessage($context, Request $request)
    {
        $user = \Auth::user();
        $defCustomer = '919152731483';
        $message = ChatMessage::findOrFail($request->get("messageId"));
        $today_date = Carbon::now()->format('Y-m-d');

        if ($context == "customer") {
            $chat_messages_count = ChatMessage::where('customer_id', $message->customer_id)->where('created_at', 'LIKE', "%$today_date%")->whereNull('number')->count();

            if ($chat_messages_count == 1) {
                $customer = Customer::find($message->customer_id);
                $params = [
                    'number' => null,
                    'user_id' => Auth::id(),
                    'approved' => 1,
                    'status' => 9,
                    'customer_id' => $message->customer_id,
                    'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'customer-info-message')->first()->reply
                ];

                $additional_message = ChatMessage::create($params);

//                if ( $customer->whatsapp_number == '919152731483' || $customer->whatsapp_number == '919152731486' ) {
                $this->sendWithThirdApi($message->customer->phone, $customer->whatsapp_number ?? $defCustomer, $additional_message->message, null, $additional_message->id);

                //$this->sendWithNewApi($message->customer->phone, $customer->whatsapp_number, $additional_message->message, null, $additional_message->id);
//                } else {
//               $this->sendWithThirdApi($message->customer->phone, $customer->whatsapp_number ?? $defCustomer, $additional_message->message, NULL, $additional_message->id);
//                    $this->sendWithNewApi( $message->customer->phone, $customer->whatsapp_number, $additional_message->message, NULL, $additional_message->id );
//                }

                sleep(5);
            }

            if (Setting::get('whatsapp_number_change') == 1) {
                $customer = Customer::find($message->customer_id);
                $default_api = ApiKey::where('default', 1)->first();

                if (!$customer->whatsapp_number_change_notified() && $default_api->number != $customer->whatsapp_number) {
                    $params = [
                        'number' => null,
                        'user_id' => Auth::id(),
                        'approved' => 1,
                        'status' => 9,
                        'customer_id' => $message->customer_id,
                        'message' => 'Our whatsapp number has changed'
                    ];

                    $additional_message = ChatMessage::create($params);

//                    if ( $default_api->number == '919152731483' ) {
                    $data = $this->sendWithThirdApi($customer->phone, $default_api->number, $additional_message->message, null, $additional_message->id);
//                    $data = $this->sendWithNewApi($customer->phone, $default_api->number, $additional_message->message, null, $additional_message->id);
//                    } else {
//                        $data = $this->sendWithNewApi( $customer->phone, $default_api->number, $additional_message->message, NULL, $additional_message->id );

//                   $this->sendWithWhatsApp($customer->phone, $default_api->number, $additional_message->message, TRUE, $additional_message->id);
//                    }


                    sleep(5);

                    CommunicationHistory::create([
                        'model_id' => $customer->id,
                        'model_type' => Customer::class,
                        'type' => 'number-change',
                        'method' => 'whatsapp'
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
        } else {
            if ($context == 'supplier') {
                $supplier = Supplier::find($message->supplier_id);
                $phone = $supplier->default_phone;
                $whatsapp_number = '971545889192';
            } else {
                if ($context == 'vendor') {
                    $vendor = Vendor::find($message->vendor_id);
                    $phone = $vendor->default_phone;
                    $whatsapp_number = $vendor->whatsapp_number;
                } else {
                    if ($context == 'task') {
                        $sender = User::find($message->user_id);

                        if ($message->erp_user == '') {
                            $receiver = Contact::find($message->contact_id);
                        } else {
                            $receiver = User::find($message->erp_user);
                        }

                        $phone = $receiver->phone;
                        $whatsapp_number = $sender->whatsapp_number;
                    } else {
                        if ($context == 'user') {
                            $sender = User::find($message->user_id);

                            if ($message->erp_user != '') {
                                $receiver = User::find($message->erp_user);
                            } else {
                                $receiver = Contact::find($message->contact_id);
                            }

                            $phone = $receiver->phone;
                            $whatsapp_number = $sender->whatsapp_number;
                        } else {
                            if ($context == 'dubbizle') {
                                $dubbizle = Dubbizle::find($message->dubbizle_id);
                                $phone = $dubbizle->phone_number;
                                $whatsapp_number = '971545889192';
                            } else {
                                if ($context == 'lawyer') {
                                    $lawyer = Lawyer::find($message->lawyer_id);
                                    $phone = $lawyer->default_phone;
                                    $whatsapp_number = $lawyer->whatsapp_number;
                                } else {
                                    if ($context == 'case') {
                                        $case = LegalCase::find($message->case_id);
                                        $lawyer = $case->lawyer;
                                        if ($lawyer) {
                                            $phone = $lawyer->default_phone;
                                        } else {
                                            $phone = '';
                                        }
                                        $whatsapp_number = $case->whatsapp_number;
                                    } else {
                                        if ($context == 'blogger') {
                                            $blogger = Blogger::find($message->blogger_id);
                                            $phone = $blogger->default_phone;
                                            $whatsapp_number = $blogger->whatsapp_number;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $data = '';
        if ($message->message != '') {

            if ($context == 'supplier' || $context == 'vendor' || $context == 'task' || $context == 'dubbizle' || $context == 'lawyer' || $context == 'case' || $context == 'blogger') {
                $this->sendWithThirdApi($phone, $whatsapp_number, $message->message, null, $message->id);
            } else {
//                if ( $whatsapp_number == '919152731483' ) {
//                $data = $this->sendWithNewApi($phone, $whatsapp_number, $message->message, null, $message->id);
                $this->sendWithThirdApi($phone, $whatsapp_number ?? $defCustomer, $message->message, null, $message->id);
//                } else {
//               $this->sendWithWhatsApp($phone, $whatsapp_number, $message->message, FALSE, $message->id);
//               $this->sendWithThirdApi($phone, $whatsapp_number ?? $defCustomer, $message->message, NULL, $message->id);
//                    $data = $this->sendWithNewApi( $phone, $whatsapp_number, $message->message, NULL, $message->id );


//                }
            }
        }

        $sendMediaFile = true;
        if ($message->media_url != '') {


            // if ($whatsapp_number == '919152731483') {
            if (1 == 2) {
                $data = $this->sendWithNewApi($phone, $whatsapp_number, null, $message->media_url, $message->id);
//              $this->sendWithWhatsApp($phone, $whatsapp_number, $message->media_url, FALSE, $message->id);

            } else {
//             $this->sendWithWhatsApp($phone, $whatsapp_number, $message->media_url, FALSE, $message->id);
                $this->sendWithThirdApi($phone, $whatsapp_number ?? $defCustomer, null, $message->media_url);
                // check here that image media url is temp created if so we can delete that
                if (strpos($message->media_url, 'instant_message_') !== false) {
                    $sendMediaFile = false;
                    $path = parse_url($message->media_url, PHP_URL_PATH);
                    if(file_exists(public_path($path)) && strpos($message->media_url, $path) !== false ){
                        @unlink( public_path($path) );
                        $message->media_url = null;
                        $message->save();
                    }
                }
            }
        }

        $images = $message->getMedia(config('constants.media_tags'));
        if (!empty($images) && $sendMediaFile) {
            $count = 0;
            foreach ($images as $key => $image) {
                $send = str_replace(' ', '%20', $image->getUrl());

                if ($context == 'task' || $context == 'vendor' || $context == 'supplier') {
                    $this->sendWithThirdApi($phone, $whatsapp_number, null, $send);
                } else {
                    // $data = $this->sendWithNewApi($phone, $whatsapp_number, NULL, $image->getUrl(), $message->id);
                    if ($count < 5) {
                        $count++;
                    } else {
                        sleep(5);

                        $count = 0;
                    }

//              if ($whatsapp_number == '919152731483') {
                    if (1 == 2) {
//                  $this->sendWithWhatsApp($phone, $whatsapp_number, $send, FALSE, $message->id);

                        SendImagesWithWhatsapp::dispatchNow($phone, $whatsapp_number, $image->getUrl(), $message->id);
                    } else {
//                  $this->sendWithWhatsApp($phone, $whatsapp_number, $send, FALSE, $message->id);
                        $this->sendWithThirdApi($phone, $whatsapp_number ?? 919152731483, null, $send);
                    }


                }
                // else if ($whatsapp_number == '919152731483') {
                // } else {
                //   $this->sendWithWhatsApp($phone, $whatsapp_number, $send, FALSE, $message->id);
                // }
            }
        }

        $message->update([
            'approved' => 1,
            'status' => 2,
            'created_at' => Carbon::now()
        ]);

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function sendToAll(Request $request, $validate = true)
    {
        if ($validate) {
            $this->validate($request, [
                // 'message'         => 'required_without:images,linked_images',
                // 'images'          => 'required_without:message|mimetypes:image/jpeg,image/png',
                // 'images.*'        => 'required_without:message|mimetypes:image/jpeg,image/png',
                'file' => 'sometimes|mimes:xlsx,xls',
                'sending_time' => 'required|date',
                'whatsapp_number' => 'required_with:file',
                'frequency' => 'required|numeric',
                'rating' => 'sometimes|nullable|numeric',
                'gender' => 'sometimes|nullable|string',
            ]);
        }

        $frequency = $request->frequency;

        if ($request->moduletype == 'customers') {
            $content[ 'message' ] = $request->body;

            foreach (json_decode($request->images) as $key => $image) {
                $media = Media::find($image);

                $content[ 'image' ][ $key ][ 'key' ] = $media->getKey();
                $content[ 'image' ][ $key ][ 'url' ] = $media->getUrl();
            }
        } else {
            $content[ 'message' ] = $request->message;

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $image) {
                    $media = MediaUploader::fromSource($image)->upload();
                    $content[ 'image' ][ $key ][ 'key' ] = $media->getKey();
                    $content[ 'image' ][ $key ][ 'url' ] = $media->getUrl();
                }
            }
        }

        if ($request->linked_images != '') {
            foreach (json_decode($request->linked_images) as $key => $id) {
                $broadcast_image = BroadcastImage::find($id);

                if ($broadcast_image->hasMedia(config('constants.media_tags'))) {
                    foreach ($broadcast_image->getMedia(config('constants.media_tags')) as $key2 => $brod_image) {
                        $content[ 'linked_images' ][ $key + $key2 ][ 'key' ] = $brod_image->getKey();
                        $content[ 'linked_images' ][ $key + $key2 ][ 'url' ] = $brod_image->getUrl();
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
                            'user_id' => Auth::id(),
                            'customer_id' => $customer->id,
                            'phone' => null,
                            'type' => 'message_all',
                            'data' => json_encode($content),
                            'sending_time' => $now,
                            'group_id' => $max_group_id
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
                            'user_id' => Auth::id(),
                            'customer_id' => $customer->id,
                            'phone' => null,
                            'type' => 'message_all',
                            'data' => json_encode($content),
                            'sending_time' => $now,
                            'group_id' => $max_group_id
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
                    $number = (int)$it[ 0 ];

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
                        'user_id' => Auth::id(),
                        'customer_id' => null,
                        'phone' => $number,
                        'whatsapp_number' => $request->whatsapp_number,
                        'type' => 'message_selected',
                        'data' => json_encode($content),
                        'sending_time' => $now,
                        'group_id' => $max_group_id
                    ]);

                    $now->addMinutes($minutes);
                }
            }
        }

        return redirect()->route('broadcast.images')->with('success', 'Messages are being sent in the background!');
    }

    public function resendMessage2(Request $request)
    {
        $messageId = $request->get('message_id');
        $message = ChatMessage::find($messageId);

        $requestData = new Request();
        $requestData->setMethod('POST');
        $requestData->request->add(['customer_id' => $message->customer_id, 'message' => $message->message, 'status' => 1]);

        return $this->sendMessage($requestData, 'customer');
    }

    public function stopAll()
    {
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
                    // DON'T THROW EXCEPTION
                    // throw new \Exception("Invalid number format. Must be 12 digits and start with 91");
                    \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Invalid number format. Must be 12 digits and start with 91: " . $number);
                    return false;
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
            $key = $keys[ 0 ][ 'key' ];

            foreach ($api_keys as $api_key) {
                if ($api_key->default == 1) {
                    $key = $api_key->key;
                }
            }
        } else {
            if (is_null($sendNumber)) {
                $keys = \Config::get("apiwha.api_keys");
                $key = $keys[ 0 ][ 'key' ];

                foreach ($api_keys as $api_key) {
                    if ($api_key->default == 1) {
                        $key = $api_key->key;
                    }
                }
            } else {
                // $config = $this->getWhatsAppNumberConfig($sendNumber);
                // $key = $config['key'];

                $keys = \Config::get("apiwha.api_keys");
                $key = $keys[ 0 ][ 'key' ];

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
        $url = "https://panel.apiwha.com/send_message.php?apikey=" . $key . "&number=" . $encodedNumber . "&text=" . $encodedText . "&custom_data=" . $encodedCustomData;
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
            // DON'T THROW EXCEPTION
            // throw new \Exception("cURL Error #:" . $err);
            \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") cURL Error for number " . $number . ":" . $err);
            return false;
        } else {
            $result = json_decode($response);
            if (!$result->success) {
                // DON'T THROW EXCEPTION
                //throw new \Exception("whatsapp request error: " . $result->description);
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") WhatsApp request error for number " . $number . ": " . $result->description);
                return false;
            } else {
                // Log successful send
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Message was sent to number " . $number . ":" . $response);
            }
        }
    }

    public function pullApiwha()
    {
        // $api_keys = ApiKey::all();
        //
        // foreach ($api_keys as $api_key) {
        //   if ($api_key->number == $number) {
        //     return;
        //   }
        // }

        $curl = curl_init();

        // $keys = \Config::get("apiwha.api_keys");
        // $key = $keys[0]['key'];
        //
        // foreach ($api_keys as $api_key) {
        //   if ($api_key->default == 1) {
        //     $key = $api_key->key;
        //   }
        // }

        // 917534013101 -
        // 919604019000 +
        // 919582881540 +
        // 919811906360 +
        // 919819119863 +
        // 916370347484 +
        // 919717147814 +
        // 919820026342 +
        // 919811455004 +
        // 919825766685 -
        // 919881790007 -
        // 919818156656 -
        // 919819749796 -
        // 919833049260 -
        // 919811912233 -
        // 919650780018 -
        // 917011450395 -
        // 919623737289 -
        // 919819363304 -
        // 919819882253 -

        $key = "Z802FWHI8E2OP0X120QR";

        $encodedNumber = urlencode('917534013101');
        // $encodedText = urlencode($text);
        $encodedType = urlencode('IN');

        //$number = "";
        $url = "https://panel.apiwha.com/get_messages.php?apikey=" . $key . "&type=" . $encodedType . "&number=" . $encodedNumber;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            // DON'T THROW EXCEPTION
            // throw new \Exception( "cURL Error #:" . $err );
            \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") cURL Error for number " . $number . ":" . $err);
            return false;
        } else {
            $result = json_decode($response, true);
            // if (!$result->success) {
            //   throw new \Exception("whatsapp request error: " . $result->description);
            //  }
        }

        // dd($result);

        $filtered_data = [];
        //
        // foreach ($result as $item) {
        //   if (Carbon::parse($item['creation_date'])->gt(Carbon::parse("2019-06-17 00:00:00"))) {
        //     $filtered_data[] = $item;
        //   }
        // }
        //
        // dd($filtered_data);

        foreach ($result as $item) {
            if (Carbon::parse($item[ 'creation_date' ])->gt(Carbon::parse("2019-06-17 00:00:00"))) {
                $filtered_data[] = $item;
                $customer = $this->findCustomerByNumber($item[ 'from' ]);

                // dd($filtered_data);

                if ($customer) {
                    $params = [
                        'number' => $item[ 'from' ],
                        'customer_id' => $customer->id,
                        'message' => $item[ 'text' ],
                        'created_at' => $item[ 'creation_date' ]
                    ];

                    // ChatMessage::create($params);
                }
            }
        }
        // array_reverse($result);
        // $result = array_values(array_sort($result, function ($value) {
        // 				return $value['creation_date'];
        // 		}));
        // //
        // 		$result = array_reverse($result);
        dd($filtered_data);


        return $result;
    }

    public function sendWithNewApi($number, $whatsapp_number = null, $message = null, $file = null, $chat_message_id = null, $enqueue = 'opportunistic')
    {
        $configs = \Config::get("wassenger.api_keys");
        $encodedNumber = "+" . $number;
        $encodedText = $message;
        $wa_token = $configs[ 0 ][ 'key' ];

        if ($whatsapp_number != null) {
            foreach ($configs as $key => $config) {
                if ($config[ 'number' ] == $whatsapp_number) {
                    $wa_device = $config[ 'device' ];

                    break;
                }

                $wa_device = $configs[ 0 ][ 'device' ];
            }
        } else {
            $wa_device = $configs[ 0 ][ 'device' ];
        }

        if ($file != null) {
            $file_exploded = explode('/', $file);
            $encoded_part = str_replace('%25', '%', urlencode(str_replace(' ', '%20', $file_exploded[ count($file_exploded) - 1 ])));
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
                CURLOPT_TIMEOUT => 180,
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
                // DON'T THROW EXCEPTION
                //throw new \Exception( "cURL Error #:" . $err );
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") cURL Error for number " . $number . ":" . $err);
                return false;
            } else {
                $result = json_decode($response, true);

                if (is_array($result)) {
                    if (array_key_exists('status', $result)) {
                        if ($result[ 'status' ] == 409) {
                            $image_id = $result[ 'meta' ][ 'file' ];
                        } else {
                            // DON'T THROW EXCEPTION
                            // throw new \Exception( "Something was wrong with image: " . $result[ 'message' ] );
                            \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Something was wrong with the image for number " . $number . ":" . $result[ 'message' ]);
                            return false;
                        }
                    } else {
                        $image_id = $result[ 0 ][ 'id' ];
                    }
                }
            }
        }
        // if (isset($response)) {
        //   throw new \Exception($response);
        // }


        $array = [
            'phone' => $encodedNumber,
            'message' => (string)$encodedText,
            'reference' => (string)$chat_message_id,
            'device' => "$wa_device",
            'enqueue' => "$enqueue",
        ];

        if (isset($image_id)) {
            $array[ 'media' ] = [
                'file' => "$image_id"
            ];
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.wassenger.com/v1/messages",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 180,
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
            // DON'T THROW EXCEPTION
            // throw new \Exception( "cURL Error #:" . $err );
            \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") cURL Error for number " . $number . ":" . $err);
            return false;
        } else {
            $result = json_decode($response, true);

            if ($http_code != 201) {
                // DON'T THROW EXCEPTION
                // throw new \Exception( "Something was wrong with message: " . $response );
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Something was wrong with the message for number " . $number . ":" . $response);
                return false;
            } else {
                // Log successful send
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Message was sent to number " . $number . ":" . $response);
            }
        }

        return $result;
    }

    public function sendWithThirdApi($number, $whatsapp_number = null, $message = null, $file = null, $chat_message_id = null, $enqueue = 'opportunistic')
    {
        // $configs = \Config::get("wassenger.api_keys");
        $encodedNumber = '+' . $number;
        $encodedText = $message;
        // $wa_token = $configs[0]['key'];
        if ($whatsapp_number == '919004780634') { // Indian
            $instanceId = "43281";
            $token = "yi841xjhrwyrwrc7";
        } elseif ($whatsapp_number == '971545889192') { // YM Dubai
            $instanceId = "62439";
            $token = "jdcqh3ladeuvwzp4";
        } else {
            if ($whatsapp_number == '919152731486') { // Solo 06
                $instanceId = '55202';
                $token = '42ndn0qg5om26vzf';
            } else {
                if ($whatsapp_number == '919152731483') { // 04
                    $instanceId = '55211';
                    $token = '3b92u5cbg215c718';
                } else { // James
//                    $instanceId = "43112";
//                    $token = "vbi9bpkoejv2lvc4";
                $instanceId = "62439";
                $token = "jdcqh3ladeuvwzp4";
                }
            }
        }


        $array = [
            'phone' => $encodedNumber
        ];

        if ($encodedText != null) {
            $array[ 'body' ] = $encodedText;
            $link = 'sendMessage';
        } else {
            $exploded = explode('/', $file);
            $filename = end($exploded);
            $array[ 'body' ] = $file;
            $array[ 'filename' ] = $filename;
            $link = 'sendFile';
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.chat-api.com/instance$instanceId/$link?token=$token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
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
            // DON'T THROW EXCEPTION
            //throw new \Exception("cURL Error #:" . $err);
            \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") cURL Error for number " . $number . ":" . $err);
            return false;
        } else {
            $result = json_decode($response, true);
            // throw new \Exception("Something was wrong with message: " . $response);
            if (!is_array($result) || array_key_exists('sent', $result) && !$result[ 'sent' ]) {
                // DON'T THROW EXCEPTION
                //throw new \Exception("Something was wrong with message: " . $response);
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Something was wrong with the message for number " . $number . ": " . $response);
                return false;
            } else {
                // Log successful send
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Message was sent to number " . $number . ":" . $response);
            }
        }

        return $result;
    }

    private function getWhatsAppNumberConfig($target)
    {
        $numbers = \Config::get("apiwha.api_keys");
        foreach ($numbers as $number) {
            if ($number[ 'number' ] == $target) {
                return $number;
            }
        }

        return $numbers[ 0 ];
    }

    private function formatChatDate($date)
    {
        return $date->format("Y-m-d h:iA");
    }

    private function modifyParamsWithMessage($params, $data)
    {
        if (filter_var($data[ 'text' ], FILTER_VALIDATE_URL)) {
            // you're good
            $path = $data[ 'text' ];
            $paths = explode("/", $path);
            $file = $paths[ count($paths) - 1 ];
            $extension = explode(".", $file)[ 1 ];
            $fileName = uniqid(true) . "." . $extension;
            $contents = file_get_contents($path);
            if (file_put_contents(implode(DIRECTORY_SEPARATOR, array(\Config::get("apiwha.media_path"), $fileName)), $contents) == false) {
                return false;
            }
            $url = implode("/", array(\Config::get("app.url"), "apiwha", "media", $fileName));
            $params[ 'media_url' ] = $url;
            return $params;
        }
        $params[ 'message' ] = $data[ 'text' ];
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
            // $params = [
            //    'number'       => NULL,
            //    'user_id'      => Auth::id(),
            //    'approved'     => 1,
            //    'status'       => 2,
            //    'customer_id'  => $customer->id,
            //    'message'      => $chat_message->message
            //  ];
            //
            // $additional_message = ChatMessage::create($params);

            if ($chat_message->message != '') {
                if ($customer->whatsapp_number == '919152731483' || $customer->whatsapp_number == '919152731486') {
                    $data = $this->sendWithNewApi($customer->phone, $customer->whatsapp_number, $chat_message->message, null, $chat_message->id);
                } else {
                    $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $chat_message->message, true, $chat_message->id);
                }
            }

            if ($chat_message->hasMedia(config('constants.media_tags'))) {
                foreach ($chat_message->getMedia(config('constants.media_tags')) as $image) {
                    if ($customer->whatsapp_number == '919152731483' || $customer->whatsapp_number == '919152731486') {
                        $data = $this->sendWithNewApi($customer->phone, $customer->whatsapp_number, null, $image->getUrl(), $chat_message->id);
                    } else {
                        $this->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, str_replace(' ', '%20', $image->getUrl()), true, $chat_message->id);
                    }
                }
            }

            $chat_message->update([
                'resent' => $chat_message->resent + 1
            ]);
        }

        if ($chat_message->erp_user != '' || $chat_message->contact_id != '') {
            $sender = User::find($chat_message->user_id);

            if ($chat_message->erp_user != '') {
                $receiver = User::find($chat_message->erp_user);
            } else {
                $receiver = Contact::find($chat_message->contact_id);
            }

            $phone = $receiver->phone;
            $whatsapp_number = $sender->whatsapp_number;
            $sending_message = $chat_message->message;

            if (preg_match_all("/Resent ([\d]+) times/i", $sending_message, $match)) {
                $sending_message = preg_replace("/Resent ([\d]+) times/i", "Resent " . ($chat_message->resent + 1) . " times", $sending_message);
            } else {
                $sending_message = 'Resent ' . ($chat_message->resent + 1) . " times. " . $sending_message;
            }

            $params = [
                'user_id' => $chat_message->user_id,
                'number' => null,
                'task_id' => $chat_message->task_id,
                'erp_user' => $chat_message->erp_user,
                'contact_id' => $chat_message->contact_id,
                'message' => $sending_message,
                'resent' => $chat_message->resent + 1,
                'approved' => 1,
                'status' => 2
            ];

            $new_message = ChatMessage::create($params);

            if ($chat_message->hasMedia(config('constants.media_tags'))) {
                foreach ($chat_message->getMedia(config('constants.media_tags')) as $image) {
                    $new_message->attachMedia($image, config('constants.media_tags'));
                }
            }

            $this->sendWithThirdApi($phone, $whatsapp_number, $new_message->message, null, $new_message->id);

            if ($task = Task::find($chat_message->task_id)) {
                if (count($task->users) > 0) {
                    if ($task->assign_from == Auth::id()) {
                        foreach ($task->users as $key => $user) {
                            if ($key != 0) {
                                $this->sendWithThirdApi($user->phone, $user->whatsapp_number, $params[ 'message' ]);
                            }
                        }
                    } else {
                        foreach ($task->users as $key => $user) {
                            if ($key != 0) {
                                if ($user->id != Auth::id()) {
                                    $this->sendWithThirdApi($user->phone, $user->whatsapp_number, $params[ 'message' ]);
                                }
                            }
                        }
                    }
                }

                if (count($task->contacts) > 0) {
                    foreach ($task->contacts as $key => $contact) {
                        if ($key != 0) {
                            $this->sendWithThirdApi($contact->phone, null, $params[ 'message' ]);
                        }
                    }
                }
            }

            if ($new_message->hasMedia(config('constants.media_tags'))) {
                foreach ($new_message->getMedia(config('constants.media_tags')) as $image) {
                    $this->sendWithThirdApi($phone, $whatsapp_number, null, $image->getUrl(), $new_message->id);
                }
            }

            $chat_message->update([
                'resent' => $chat_message->resent + 1
            ]);
        }

        return response()->json([
            'resent' => $chat_message->resent
        ]);
    }


    public function createGroup($task_id = null, $group_id = null, $number , $message = null , $whatsapp_number)
    {

         $encodedText = $message;

        if ($whatsapp_number == '919004780634') { // Indian
            $instanceId = "43281";
            $token = "yi841xjhrwyrwrc7";
        } elseif ($whatsapp_number == '971545889192') { // YM Dubai
            $instanceId = "62439";
            $token = "jdcqh3ladeuvwzp4";
        } else {
            if ($whatsapp_number == '919152731486') { // Solo 06
                $instanceId = '55202';
                $token = '42ndn0qg5om26vzf';
            } else {
                if ($whatsapp_number == '919152731483') { // 04
                    $instanceId = '55211';
                    $token = '3b92u5cbg215c718';
                } else { // James
//                    $instanceId = "43112";
//                    $token = "vbi9bpkoejv2lvc4";
                $instanceId = "62439";
                $token = "jdcqh3ladeuvwzp4";
                }
            }
        }


       if($task_id != null){
            $id = (string) $task_id;

           $array = [
            'groupName' => $id,
            'phones' => $number,

            ];
           $link = 'group';

        }else{
            $id = (string) $group_id;

            $array = [
            'groupId' => $id,
            'participantPhone' => $number,
           ];
           $link = 'addGroupParticipant';
        }

       $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.chat-api.com/instance$instanceId/$link?token=$token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
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

        $result = json_decode($response, true);

        if ($err) {
            // DON'T THROW EXCEPTION
            //throw new \Exception("cURL Error #:" . $err);
            \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") cURL Error for number " . $number . ":" . $err);
            return false;
        } else {
            $result = json_decode($response, true);
            // throw new \Exception("Something was wrong with message: " . $response);
            if (!is_array($result) || array_key_exists('sent', $result) && !$result[ 'sent' ]) {
                // DON'T THROW EXCEPTION
                //throw new \Exception("Something was wrong with message: " . $response);
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Something was wrong with the message for number " . $response);
                return false;
            } else {
                // Log successful send
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Message was sent to number ". $response);
            }
        }
         return $result;
    }


}
