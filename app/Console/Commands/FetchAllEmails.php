<?php

namespace App\Console\Commands;

use App\CashFlow;
use App\CronJobReport;
use App\Email;
use App\EmailAddress;
use App\EmailRunHistories;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Webklex\IMAP\Client;
use EmailReplyParser\Parser\EmailParser;
use App\ChatMessagesQuickData;
use App\ChatMessage;
use Illuminate\Support\Facades\DB;

/**
 * @author Sukhwinder <sukhwinder@sifars.com>
 * This command takes care of receiving all the emails from the smtp set in the environment
 *
 * All fetched emails will go inside emails table
 */
class FetchAllEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:all_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches all emails from the configured SMTP settings';

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

        $report = CronJobReport::create([
            'signature'  => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        $emailAddresses = EmailAddress::orderBy('id', 'asc')->get();

        foreach ($emailAddresses as $emailAddress) {
            try {
                $imap = new Client([
                    'host'          => $emailAddress->host,
                    'port'          => 993,
                    'encryption'    => "ssl",
                    'validate_cert' => true,
                    'username'      => $emailAddress->username,
                    'password'      => $emailAddress->password,
                    'protocol'      => 'imap',
                ]);

                $imap->connect();

                $types = [
                    'inbox' => [
                        'inbox_name' => 'INBOX',
                        'direction'  => 'from',
                        'type'       => 'incoming',
                    ],
                    'sent'  => [
                        'inbox_name' => 'INBOX.Sent',
                        'direction'  => 'to',
                        'type'       => 'outgoing',
                    ],
                ];

                $available_models = [
                    "supplier" => \App\Supplier::class, "vendor" => \App\Vendor::class,
                    "customer" => \App\Customer::class, "users"  => \App\User::class,
                ];
                $email_list = [];
                foreach ($available_models as $key => $value) {
                    $email_list[$value] = $value::whereNotNull('email')->pluck('id', 'email')->unique()->all();
                }

                foreach ($types as $type) {

                    dump("Getting emails for: " . $type['type']);

                    $inbox = $imap->getFolder($type['inbox_name']);
                    if ($type['type'] == "incoming") {
                        $latest_email = Email::where('to', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                    } else {
                        $latest_email = Email::where('from', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                    }

                    $latest_email_date = $latest_email ? Carbon::parse($latest_email->created_at) : false;

                    dump("Last received at: " . ($latest_email_date ?: 'never'));
                    // Uncomment below just for testing purpose
                    //                    $latest_email_date = Carbon::parse('2020-01-01');

                    if ($latest_email_date) {
                        $emails = $inbox->messages()->where('SINCE', $latest_email_date->subDays(1)->format('d-M-Y'));
                    } else {
                        $emails = $inbox->messages();
                    }

                    $emails = $emails->get();

                    //
                    // dump($inbox->messages()->where([
                    //     ['SINCE', $latest_email_date->subDays(1)->format('d-M-Y')],
                    //     ])->get());
                    foreach ($emails as $email) {

                        $reference_id = $email->references;
                        //                        dump($reference_id);
                        $origin_id = $email->message_id;

                        // Skip if message is already stored
                        if (Email::where('origin_id', $origin_id)->count() > 0) {
                            continue;
                        }

                        // check if email has already been received

                        if ($email->hasHTMLBody()) {
                            $content = $email->getHTMLBody();
                        } else {
                            $content = $email->getTextBody();
                        }

                        $email_subject = $email->getSubject();
                        \Log::channel('customer')->info("Subject  => ".$email_subject);

                        //if (!$latest_email_date || $email->getDate()->timestamp > $latest_email_date->timestamp) {
                        $attachments_array = [];
                        $attachments       = $email->getAttachments();
                        $fromThis          = $email->getFrom()[0]->mail;
                        $attachments->each(function ($attachment) use (&$attachments_array, $fromThis, $email_subject) {
                            $attachment->name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $attachment->name);
                            file_put_contents(storage_path('app/files/email-attachments/' . $attachment->name), $attachment->content);
                            $path = "email-attachments/" . $attachment->name;

                            $attachments_array[] = $path;

                            /*start 3215 attachment fetch from DHL mail */
                            \Log::channel('customer')->info("Match Start  => ".$email_subject);

                            $findFromEmail = explode('@', $fromThis);
                            if (strpos(strtolower($email_subject), "your copy invoice") !== false && isset($findFromEmail[1]) && (strtolower($findFromEmail[1]) == 'dhl.com')) {
                                \Log::channel('customer')->info("Match Found  => ".$email_subject);
                                $this->getEmailAttachedFileData($attachment->name);
                            }
                            /*end 3215 attachment fetch from DHL mail */
                        });

                        $from = $email->getFrom()[0]->mail;
                        $to   = array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail;

                        // Model is sender if its incoming else its receiver if outgoing
                        if ($type['type'] == 'incoming') {
                            $model_email = $from;
                        } else {
                            $model_email = $to;
                        }

                        // Get model id and model type

                        extract($this->getModel($model_email, $email_list));
                        /**
                         * @var $model_id
                         * @var $model_type
                         */

                        $subject = explode("#", $email_subject);
                        if (isset($subject[1]) && !empty($subject[1])) {
                            $findTicket = \App\Tickets::where('ticket_id', $subject[1])->first();
                            if ($findTicket) {
                                $model_id   = $findTicket->id;
                                $model_type = \App\Tickets::class;
                            }
                        }

                        $params = [
                            'model_id'        => $model_id,
                            'model_type'      => $model_type,
                            'origin_id'       => $origin_id,
                            'reference_id'    => $reference_id,
                            'type'            => $type['type'],
                            'seen'            => $email->getFlags()['seen'],
                            'from'            => $email->getFrom()[0]->mail,
                            'to'              => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                            'subject'         => $email->getSubject(),
                            'message'         => $content,
                            'template'        => 'customer-simple',
                            'additional_data' => json_encode(['attachment' => $attachments_array]),
                            'created_at'      => $email->getDate(),
                        ];
                        //                            dump("Received from: ". $email->getFrom()[0]->mail);
                        Email::create($params);

                        $historyParam = [
                            'email_address_id'        => $emailAddress->id,
                            'is_success'              => 1,
                        ];
                        EmailRunHistories::create($historyParam);

                        if ($type['type'] == 'incoming') {
                            $message = trim($content);

                            $reply    = \App\WatsonAccount::getReply($message);

                            $reply    = (new EmailParser())->parse($reply);
                            $fragment = current($reply->getFragments());
                            
                            if ($reply) {
                                $params = [
                                    'model_id' => $model_id,
                                    'model_type' => $model_type,
                                    'origin_id' => $origin_id,
                                    'reference_id' => $reference_id,
                                    'type' => 'outgoing',
                                    'seen' => $email->getFlags()['seen'],
                                    'from' => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                    'to' => $email->getFrom()[0]->mail,
                                    'subject' => $email->getSubject(),
                                    'message' => $fragment->getContent(),
                                    'template' => 'customer-simple',
                                    'additional_data' => json_encode(['attachment' => []]),
                                    'created_at' => $email->getDate(),
                                    'approve_mail' => 1,
                                ];
                                Email::create($params);
                            }
                        }
                        $cutomer = \App\customers::where( 'email' , $email->getFrom()[0]->mail )->get();
                        $this->whatappSend( $cutomer , $fragment->getContent() );
                        $this->sendwatson();
                        //}
                    }
                }

                dump('__________');

                $report->update(['end_time' => Carbon::now()]);
            } catch (\Exception $e) {
                \Log::channel('customer')->info($e->getMessage());
                $historyParam = [
                    'email_address_id'        => $emailAddress->id,
                    'is_success'              => 0,
                    'message'                 => $e->getMessage()
                ];
                EmailRunHistories::create($historyParam);
                \App\CronJob::insertLastError($this->signature, $e->getMessage());
            }
        }
    }

    /**
     * Check all the emails in the DB and extract the model type from there
     *
     * @param [type] $email
     * @param [type] $email_list
     * @return array(model_id,miodel_type)
     */
    private function getModel($email, $email_list)
    {
        $model_id   = null;
        $model_type = null;

        // Traverse all models
        foreach ($email_list as $key => $value) {

            // If email exists in the DB
            if (isset($value[$email])) {
                $model_id   = $value[$email];
                $model_type = $key;
                break;
            }
        }

        return compact('model_id', 'model_type');
    }

    public function getEmailAttachedFileData($fileName = '')
    {
        $file = fopen(storage_path('app/files/email-attachments/' . $fileName), "r");

        $skiprowupto           = 1; //skip first line
        $rowincrement          = 1;
        $attachedFileDataArray = array();
        while (($data = fgetcsv($file, 4000, ",")) !== false) {
            ///echo '<pre>'.print_r($data,true).'</pre>'; die('developer working');
            /* foreach($data as $d){
            $d=str_replace('(','',$d);
            $d=str_replace(')','',$d);
            $d=str_replace('.','',$d);
            $d=str_replace('&','',$d);
            $d=str_replace(' ','_',$d);
            $d=strtolower($d);
            //echo $csvArrayIndex.' - $table->string("'.$d.'")->nullable();'."\n";
            //echo '"'.$d.'"=>$data['.$csvArrayIndex.']'."\n";
            $csvArrayIndex++;
            }
            exit; */
            if ($rowincrement > $skiprowupto) {
                //echo '<pre>'.print_r($data = fgetcsv($file, 4000, ","),true).'</pre>';
                if (isset($data[0]) && !empty($data[0])) {
                    try {
                        $due_date              = date('Y-m-d', strtotime($data[9]));
                        $attachedFileDataArray = array(
                            "line_type"                       => $data[0],
                            "billing_source"                  => $data[1],
                            "original_invoice_number"         => $data[2],
                            "invoice_number"                  => $data[3],
                            "invoice_identifier"              => $data[5],
                            "invoice_type"                    => $data[6],
                            "invoice_currency"                => $data[69],
                            "invoice_amount"                  => $data[70],
                            "invoice_type"                    => $data[6],
                            "invoice_date"                    => $data[7],
                            "payment_terms"                   => $data[8],
                            "due_date"                        => $due_date,
                            "billing_account"                 => $data[11],
                            "billing_account_name"            => $data[12],
                            "billing_account_name_additional" => $data[13],
                            "billing_address_1"               => $data[14],
                            "billing_postcode"                => $data[17],
                            "billing_city"                    => $data[18],
                            "billing_state_province"          => $data[19],
                            "billing_country_code"            => $data[20],
                            "billing_contact"                 => $data[21],
                            "shipment_number"                 => $data[23],
                            "shipment_date"                   => $data[24],
                            "product"                         => $data[30],
                            "product_name"                    => $data[31],
                            "pieces"                          => $data[32],
                            "origin"                          => $data[33],
                            "orig_name"                       => $data[34],
                            "orig_country_code"               => $data[35],
                            "orig_country_name"               => $data[36],
                            "senders_name"                    => $data[37],
                            "senders_city"                    => $data[42],
                            'created_at'                      => \Carbon\Carbon::now(),
                            'updated_at'                      => \Carbon\Carbon::now(),
                        );
                        if (!empty($attachedFileDataArray)) {
                            $attachresponse = \App\Waybillinvoice::create($attachedFileDataArray);

                            // check that way bill exist not then create
                            $wayBill = \App\Waybill::where("awb", $attachresponse->shipment_number)->first();
                            if (!$wayBill) {
                                $wayBill      = new \App\Waybill;
                                $wayBill->awb = $attachresponse->shipment_number;

                                $wayBill->from_customer_name      = $data[45];
                                $wayBill->from_city               = $data[42];
                                $wayBill->from_country_code       = $data[44];
                                $wayBill->from_customer_address_1 = $data[38];
                                $wayBill->from_customer_address_2 = $data[39];
                                $wayBill->from_customer_pincode   = $data[41];
                                $wayBill->from_company_name       = $data[39];

                                $wayBill->to_customer_name      = $data[50];
                                $wayBill->to_city               = $data[55];
                                $wayBill->to_country_code       = $data[57];
                                $wayBill->to_customer_phone     = "";
                                $wayBill->to_customer_address_1 = $data[51];
                                $wayBill->to_customer_address_2 = $data[52];
                                $wayBill->to_customer_pincode   = $data[54];
                                $wayBill->to_company_name       = "";

                                $wayBill->actual_weight = $data[68];
                                $wayBill->volume_weight = $data[66];

                                $wayBill->cost_of_shipment = $data[70];
                                $wayBill->package_slip     = $attachresponse->shipment_number;
                                $wayBill->pickup_date      = date("Y-m-d", strtotime($data[24]));
                                $wayBill->save();
                            }

                            $cash_flow = new CashFlow();
                            $cash_flow->fill([
                                'date'                => $attachresponse->due_date ? $attachresponse->due_date : null,
                                'type'                => 'pending',
                                'description'         => 'Waybill invoice details',
                                'cash_flow_able_id'   => $attachresponse->id,
                                'cash_flow_able_type' => \App\Waybillinvoice::class,
                            ])->save();

                        }
                    }catch(\Exception $e) {
                        \Log::error("Error from the dhl invoice : ".$e->getMessage());
                    }
                    
                }
            }
            $rowincrement++;
        }
        fclose($file);
    }

    private function whatappSend( $customer = null, $message = null )
    {
        if ($customer) {
            // $exp_mesaages = explode(" ", $params['message']);
            $exp_mesaages = explode( " ", $message );
            for ($i = 0; $i < count($exp_mesaages); $i++) {
                $keywordassign = DB::table('keywordassigns')->select('*')
                    ->whereRaw('FIND_IN_SET(?,keyword)', [strtolower($exp_mesaages[$i])])
                    ->get();
                if (count($keywordassign) > 0) {
                    break;
                }
            }

            if (count($keywordassign) > 0) {
                $task_array = array(
                    "category" => 42,
                    "is_statutory" => 0,
                    "task_subject" => "#" . $customer->id . "-" . $keywordassign[0]->task_description,
                    "task_details" => $keywordassign[0]->task_description,
                    "assign_from" => \App\User::USER_ADMIN_ID,
                    "assign_to" => $keywordassign[0]->assign_to,
                    "customer_id" => $customer->id,
                    "created_at" => date("Y-m-d H:i:s"),
                    "updated_at" => date("Y-m-d H:i:s")
                );
                DB::table('tasks')->insert($task_array);
                $taskid = DB::getPdo()->lastInsertId();
                $task_users_array = array(
                    "task_id" => $taskid,
                    "user_id" => $keywordassign[0]->assign_to,
                    "type" => "App\User"
                );
                DB::table('task_users')->insert($task_users_array);

                // check that match if this the assign to is auto user 
                // then send price and deal
                \Log::channel('whatsapp')->channel('whatsapp')->info("Price Lead section started for customer id : " . $customer->id);
                if ($keywordassign[0]->assign_to == self::AUTO_LEAD_SEND_PRICE) {
                    \Log::channel('whatsapp')->info("Auto section started for customer id : " . $customer->id);
                    if (!empty($parentMessage)) {
                        \Log::channel('whatsapp')->info("Auto section parent message found started for customer id : " . $customer->id);
                        $parentMessage->sendLeadPrice($customer);
                    }
                }

                //START CODE Task message to send message in whatsapp

                $task_info = DB::table('tasks')
                    ->select('*')
                    ->where('id', '=', $taskid)
                    ->get();

                $users_info = DB::table('users')
                    ->select('*')
                    ->where('id', '=', $task_info[0]->assign_to)
                    ->get();

                if (count($users_info) > 0) {
                    if ($users_info[0]->phone != "") {
                        $params_task = [
                            'number' => NULL,
                            'user_id' => $users_info[0]->id,
                            'approved' => 1,
                            'status' => 2,
                            'task_id' => $taskid,
                            'message' => $task_info[0]->task_details,
                            'quoted_message_id' => $quoted_message_id
                        ];
                        // app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($users_info[0]->phone, $users_info[0]->whatsapp_number, $task_info[0]->task_details);

                        $chat_message = \ChatMessage::create($params_task);
                        ChatMessagesQuickData::updateOrCreate([
                            'model' => \App\Task::class,
                            'model_id' => $taskid
                        ], [
                            'last_communicated_message' => $task_info[0]->task_details,
                            'last_communicated_message_at' => $chat_message->created_at,
                            'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
                        ]);

                        $myRequest = new Request();
                        $myRequest->setMethod('POST');
                        $myRequest->request->add(['messageId' => $chat_message->id]);

                        // app('App\Http\Controllers\WhatsAppController')->approveMessage('task', $myRequest);
                    }
                }
                //END CODE Task message to send message in whatsapp
            }
        }
    }

    private function sendwatson( $customer = null, $message = null ){
        if ( array_key_exists('message', $message) && (preg_match("/price/i", $message) || preg_match("/you photo/i",$message) || preg_match("/pp/i", $message) || preg_match("/how much/i", $message) || preg_match("/cost/i", $message) || preg_match("/rate/i", $message))) {
            if ($customer = Customer::find( $customer )) {

                // send price from meessage queue
                $messageSentLast = \App\MessageQueue::where("customer_id", $customer->id)->where("sent", 1)->orderBy("sending_time", "desc")->first();
                // if message found then start
                $selected_products = [];
                if ($messageSentLast) {
                    $mqProducts = $messageSentLast->getImagesWithProducts();
                    if (!empty($mqProducts)) {
                        foreach ($mqProducts as $mq) {
                            if (!empty($mq["products"])) {
                                foreach ($mq["products"] as $productId) {
                                    $selected_products[] = $productId;
                                }
                            }
                        }
                    }
                }

                // check the last message send for price
                // $lastChatMessage = \App\ChatMessage::getLastImgProductId($customer->id);
                // if ($lastChatMessage) {
                //     if ($lastChatMessage->hasMedia(config('constants.attach_image_tag'))) {
                //         $lastImg = $lastChatMessage->getMedia(config('constants.attach_image_tag'))->sortByDesc('id')->first();
                //         if ($lastImg) {
                //             $mediable = \DB::table("mediables")->where("media_id", $lastImg->id)->where('mediable_type', Product::class)->first();
                //             if (!empty($mediable)) {
                //                 $product = Product::find($mediable->mediable_id);
                //                 if (!empty($product)) {
                //                     $priceO = ($product->price_inr_special > 0) ? $product->price_inr_special : $product->price_inr;
                //                     $selected_products[] = $product->id;
                //                     $temp_img_params = $params;
                //                     $temp_img_params['message'] = "Price : " . $priceO;
                //                     $temp_img_params['media_url'] = null;
                //                     $temp_img_params['status'] = 2;
                //                     // Create new message
                //                     ChatMessage::create($temp_img_params);
                //                 }
                //             }
                //         }
                //     }
                // }

                if (!empty($selected_products) && $messageSentLast) {
                    foreach ($selected_products as $pid) {
                        $product = \App\Product::where("id", $pid)->first();
                        $quick_lead = \App\ErpLeads::create([
                            'customer_id' => $customer->id,
                            //'rating' => 1,
                            'lead_status_id' => 3,
                            //'assigned_user' => 6,
                            'product_id' => $pid,
                            'brand_id' => $product ? $product->brand : null,
                            'category_id' => $product ? $product->category : null,
                            'brand_segment' => $product && $product->brands ? $product->brands->brand_segment : null,
                            'color' => $customer->color,
                            'size' => $customer->size,
                            'created_at' => Carbon::now()
                        ]);
                    }

                    $requestData = new Request();
                    $requestData->setMethod('POST');
                    $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $selected_products]);

                    app('App\Http\Controllers\LeadsController')->sendPrices($requestData, new GuzzleClient);

                    CommunicationHistory::create([
                        'model_id' => $messageSentLast->id,
                        'model_type' => \App\MessageQueue::class,
                        'type' => 'broadcast-prices',
                        'method' => 'email'
                    ]);
                }

                Instruction::create([
                    'customer_id' => $customer->id,
                    'instruction' => 'Please send the prices',
                    'category_id' => 1,
                    'assigned_to' => 7,
                    'assigned_from' => 6
                ]);
            }

            if (!empty($message)) {

                $replies = ChatbotQuestion::join('chatbot_question_examples', 'chatbot_questions.id', 'chatbot_question_examples.chatbot_question_id')
                    ->join('chatbot_questions_reply', 'chatbot_questions.id', 'chatbot_questions_reply.chatbot_question_id')
                    ->where('chatbot_questions_reply.store_website_id', ($customer->store_website_id) ? $customer->store_website_id : 1)
                    ->select('chatbot_questions.value','chatbot_questions.keyword_or_question','chatbot_questions.erp_or_watson','chatbot_questions.auto_approve','chatbot_question_examples.question','chatbot_questions_reply.suggested_reply')
                    ->where('chatbot_questions.erp_or_watson', 'erp')
                    ->get();

                $isReplied = 0;


                $chatbotReply = \App\ChatbotReply::create([
                    "question" => $params['message'],
                    "replied_chat_id" => $message->id
                ]);

                \Log::channel('whatsapp')->info("reached step 3 here");

                foreach ($replies as $reply) {
                    if($params['message'] != '' && $customer && array_key_exists('message', $params)){
                        $keyword = $reply->question;
                        if(($keyword == $params['message'] || strpos(strtolower(trim($keyword)), strtolower(trim($params['message']))) !== false) && $reply->suggested_reply) {
                            /*if($reply->auto_approve) {
                                $status = 2;
                            }
                            else {
                                $status = 8; 
                            }*/
                            $status = ChatMessage::CHAT_AUTO_WATSON_REPLY;
                            $temp_params = $params;
                            $temp_params['message'] = $reply->suggested_reply;
                            $temp_params['media_url'] = null;
                            $temp_params['status'] = $status;
                            $temp_params['question_id'] = $reply->id;

                            // Create new message
                            $message = ChatMessage::create($temp_params);

                            if ($message->status == ChatMessage::CHAT_AUTO_WATSON_REPLY) {
                                $chatbotReply->chat_id = $message->id;
                                $chatbotReply->answer = $reply->suggested_reply;
                                $chatbotReply->reply = '{"output":{"database":[{"response_type":"text","text":"'.$reply->suggested_reply.'"}]}}';
                                $chatbotReply->reply_from = 'erp';
                                $chatbotReply->save();
                            }

                            // Send message if all required data is set
                            if ($temp_params['message'] || $temp_params['media_url']) {
                                if($status == 2) {
                                    $sendResult = $this->sendWithThirdApi($customer->phone, isset($instanceNumber) ? $instanceNumber : null, $temp_params['message'], $temp_params['media_url']);
                                    if ($sendResult) {
                                        $message->unique_id = $sendResult['id'] ?? '';
                                        $message->save();
                                    }
                                }
                                $isReplied = 1;
                                break;
                            }
                        }
                    }
                }


                // assigned the first storewebsite to default erp customer
                $customer->store_website_id = ($customer->store_website_id > 0) ? $customer->store_website_id : 1;
                if(!$isReplied && $customer->store_website_id) {
                    WatsonManager::sendMessage($customer,$params['message'],false , null , $message);
                }
            }
        }
    }
}
