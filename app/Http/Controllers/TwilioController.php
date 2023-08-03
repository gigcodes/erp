<?php

/**
 * Class TwilioController | app/Http/Controllers/TwilioController.php
 * Twilio integration for VOIP purpose using Twilio's Voice REST API
 *
 * @filesource required php 7 as this file contains tokenizer extension which was not stable prior to this version
 *
 * @see https://www.twilio.com/docs/voice/quickstart/php
 * @see FindByNumberController
 *
 * @author   sololux <sololux@gmail.com>
 */

namespace App\Http\Controllers;

use App\User;
use Response;
use App\Brand;
use App\Leads;
use App\Order;
use Validator;
use App\Status;
use App\Helpers;
use App\Message;
use App\Product;
use App\Setting;
use App\Category;
use App\Customer;
use App\RoleUser;
use Twilio\Twiml;
use App\Recording;
use App\TwilioLog;
use Carbon\Carbon;
use App\CallHistory;
use App\ChatMessage;
use App\OrderStatus;
use App\TwilioAgent;
use App\OrderProduct;
use App\StoreWebsite;
use App\TwilioWorker;
use App\CallRecording;
use App\ReplyCategory;
use App\ReturnExchange;
use App\TwilioActivity;
use App\TwilioCallData;
use App\TwilioPriority;
use App\TwilioWorkflow;
use Twilio\Rest\Client;
use App\AgentCallStatus;
use App\CallBusyMessage;
use App\ChatbotQuestion;
use App\TwilioCallBlock;
use App\TwilioCondition;
use App\TwilioKeyOption;
use App\TwilioTaskQueue;
use App\TwilioWorkspace;
use App\TwilioAccountLog;
use App\TwilioCredential;
use App\TwilioCallJourney;
use App\TwilioCallWaiting;
use App\TwilioDequeueCall;
use App\TwilioMessageTone;
use App\TwilioActiveNumber;
use App\TwilioSitewiseTime;
use App\TwilioWebhookError;
use Illuminate\Support\Str;
use Twilio\Jwt\ClientToken;
use App\ChatbotTypeErrorLog;
use App\TwilioCallStatistic;
use Illuminate\Http\Request;
use App\Helpers\TwilioHelper;
use App\ReturnExchangeStatus;
use App\TwilioCallForwarding;
use App\CallBusyMessageStatus;
use App\ReturnExchangeProduct;
use Twilio\TwiML\VoiceResponse;
use App\StoreWebsiteTwilioNumber;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Twilio\Jwt\TaskRouter\WorkerCapability;
use App\Models\Twilio\TwilioMessageDeliveryLogs;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\LogRequest;
use App\Models\TwilioConditionStatus;


/**
 * Class TwilioController - active record
 *
 * A Twillio class which is extending FindBYNumber controller class
 * This class is used to make and receive phone calls with Twilio Programmable Voice.
 */
class TwilioController extends FindByNumberController
{
    public function __construct()
    {
        \Debugbar::disable();
    }

    /**
     * Twillio Account SID and Auth Token from twilio.com/console
     * Initilizing the Twilio client
     *
     * @todo Function is not used anywhere.
     *
     * @return Twilio Object
     *
     * @uses Client
     * @uses Config
     */
    private function getTwilioClient()
    {
        return new Client(\Config::get('twilio.account_sid'), \Config::get('twilio.auth_token'));
    }

    /**
     * Create a token for the twilio device which expires after 1 min
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\JsonResponse
     *
     * @Rest\Post("twilio/token")
     *
     * @uses Auth
     * @uses ClientToken
     */
    public function acceptCall(Request $request)
    {
        $response = new VoiceResponse();
        $url = 'https://' . $request->getHost() . '/twilio/recordingStatusCallback';
        // $actionurl = \Config::get("app.url") . "/twilio/handleDialCallStatus";
        $actionurl = 'https://' . $request->getHost() . '/twilio/handleDialCallStatus';
        //$recordurl = 'https://'.$request->getHost() . "/twilio/storetranscript";
        $dial = $response->dial('+919463488313', [
            'record' => 'true',
            'recordingStatusCallback' => $url,
            'action' => $actionurl,
            'timeout' => '60',
        ]);

        $dial->client(10410);

        return $response;
    }

    public function createToken(Request $request)
    {
//        return response()->json(['agent' => \Auth::check()]);

        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);

        if (\Auth::check()) {
            $user = \Auth::user();
            $user_id = $user->id;
            // $agent = str_replace('-', '_', Str::slug($user->name));
            // $agent = 'yogesh';

            $check_is_agent = TwilioAgent::where('user_id', $user_id)->where('status', 1)->first();
            if ($check_is_agent && in_array('check_is_agent', $conditions)) {
                $twilio_active_credential = StoreWebsiteTwilioNumber::join('twilio_active_numbers', 'twilio_active_numbers.id', 'store_website_twilio_numbers.twilio_active_number_id')
                ->where('store_website_twilio_numbers.store_website_id', $check_is_agent->store_website_id)
                ->select('twilio_active_numbers.twilio_credential_id', 'twilio_active_numbers.workspace_sid')
                ->first();
                Log::channel('customerDnd')->info('twilio_active_credential ==> ' . $twilio_active_credential->twilio_credential_id);

                $devices = TwilioCredential::where('status', 1)->whereNotNull('twiml_app_sid')->where('id', $twilio_active_credential->twilio_credential_id)->get();

                if ($devices && in_array('devices', $conditions)) {
                    $agent = 'customer_call_agent_' . $user_id;
                    if ($devices->count()) {
                        $tokens = [];
                        $workers = [];
                        foreach ($devices as $device) {
                            $capability = new ClientToken($device->account_id, $device->auth_token);
                            // $capability->allowClientOutgoing(\Config::get("twilio.webrtc_app_sid"));
                            $capability->allowClientOutgoing($device->twiml_app_sid);

                            $capability->allowClientIncoming($agent);
                            $expiresIn = (3600 * 8);
                            $token = $capability->generateToken($expiresIn);
                            $tokens[] = $token;

                            $twilioWorkspace = TwilioWorkspace::where('workspace_sid', $twilio_active_credential->workspace_sid)->firstOrFail();
                            $twilioWorker = TwilioWorker::where('user_id', $user_id)->where('twilio_workspace_id', $twilioWorkspace->id)->firstOrFail();

                            $capability = new WorkerCapability($device->account_id, $device->auth_token, $twilio_active_credential->workspace_sid, $twilioWorker->worker_sid);
                            $capability->allowFetchSubresources();
                            $capability->allowActivityUpdates();
                            $capability->allowReservationUpdates();
                            $workers[] = $capability->generateToken($expiresIn);
                        }

                        return response()->json(['twilio_tokens' => $tokens, 'workers' => $workers, 'agent' => $agent]);
                    }

                    return response()->json(['empty' => true]);
                }

                return response()->json(['empty' => true]);
            } else {
                return response()->json(['empty' => true]);
            }

            // $agent = 'customer_call_agent_'.$user_id;
            // // $agent = 'customer_call_agent_6';

            // $devices = TwilioCredential::where('status',1)->where('twiml_app_sid','!=',null)->get();
            // if ($devices->count()){
            //     $tokens=[];
            //     foreach ($devices as $device){
            //         $capability = new ClientToken($device->account_id, $device->auth_token);
            //         // $capability->allowClientOutgoing(\Config::get("twilio.webrtc_app_sid"));
            //         $capability->allowClientOutgoing($device->twiml_app_sid);

            //         $capability->allowClientIncoming($agent);
            //         $expiresIn = (3600 * 1);
            //         $token = $capability->generateToken();
            //         $tokens[]=$token;
            //     }
            //     return response()->json(['twilio_tokens' => $tokens, 'agent' => $agent]);

            // }
            // return response()->json(['empty' => true]);

//            $capability = new ClientToken(\Config::get("twilio.account_sid"), \Config::get("twilio.auth_token"));
//            $capability->allowClientOutgoing(\Config::get("twilio.webrtc_app_sid"));
//            $capability->allowClientIncoming($agent);
//            $expiresIn = (3600 * 1);
//            $token = $capability->generateToken();
//            return response()->json(['twilio_token' => $token, 'agent' => $agent]);
        }

        return response()->json(['empty' => true]);
    }

    /**
     * This function is use for create Twilio log
     *
     * @param type [array] inputArray
     * @param  Request  $request Request
     *  @return void;
     */
    public function createTwilioLog(Request $request, $inputArray = [], $logType = '', $type = 'default')
    {
        try {
            $number = $request->get('From') ?? 0;
            $call_sid = $request->get('CallSid') ?? 0;
            $account_sid = $request->get('AccountSid') ?? 0;
            foreach ($inputArray as $requestDataKey => $requestDataVal) {
                if (is_array($requestDataVal)) {
                    foreach ($requestDataVal as $reqKey => $reqVal) {
                        if (is_array($reqVal)) {
                            $reqValEncode = json_encode($reqVal);
                        } else {
                            $reqValEncode = $reqVal;
                        }
                        TwilioLog::create([
                            'log' => $logType . ' ' . $reqKey . ' : ' . $reqValEncode,
                            'account_sid' => $account_sid,
                            'call_sid' => $call_sid,
                            'phone' => $number,
                            'type' => $type,
                        ]);
                    }
                } else {
                    TwilioLog::create([
                        'log' => $logType . ' ' . $requestDataKey . ' : ' . $requestDataVal,
                        'account_sid' => $account_sid,
                        'call_sid' => $call_sid,
                        'phone' => $number,
                        'type' => $type,
                    ]);
                }
            }
        } catch (\Exception $e) {
            TwilioLog::create(['log' => 'Workflow Log ' . $e->getMessage(), 'account_sid' => 0, 'call_sid' => 0, 'phone' => 0]);
        }
    }

    public function twilioEvents(Request $request, Client $twilioClient)
    {
        $missedCallEvents = config('services.twilio')['missedCallEvents'];
        // Create Twilio Log
        $inputArray = $request->input();
        $this->createTwilioLog($request, $inputArray, 'Twilio Event Method Called : Request Data is');
        //TwilioLog::create(['log'=>'Twilio Event Method Called : Request Data is => '.json_encode($request->input())]);
        $eventTypeName = $request->input('EventType');
        if (in_array($eventTypeName, $missedCallEvents) and strtolower($eventTypeName) == "$eventTypeName") {
            $taskAttr = $this->parseAttributes('TaskAttributes', $request);
            if (! empty($taskAttr)) {
                // Create Twilio Log
                $this->createTwilioLog($request, $inputArray, 'Twilio Missed Call Events Occur : Request Data is');
                //TwilioLog::create(['log'=>'Twilio Missed Call Events Occur : Request Data is => '.json_encode($request->input())]);
                $call = CallBusyMessage::where('caller_sid', $taskAttr->call_sid)->first();
                $status = CallBusyMessageStatus::where('name', 'Reserved')->pluck('id')->first();
                if ($call != null) {
                    $call->update('call_busy_message_statuses_id', $status);
                } else {
                    CallBusyMessage::create(['twilio_call_sid' => $taskAttr->caller,
                        'caller_sid' => $taskAttr->call_sid, 'call_busy_message_statuses_id' => $status, ]);
                }
            }
        }
    }

    public function parseAttributes($name, $request)
    {
        $attrJson = $request->input($name);

        return json_decode($attrJson);
    }

    /**
     * Incoming call URL for Twilio programmable voice
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio/incoming")
     *
     * @uses Log
     * @uses Twiml
     */
    public function incomingCall(Request $request)
    {
        $number = $request->get('From');
        $twilioNumber = $request->get('To');

        Log::channel('customerDnd')->info('Enter in Incoming Call Section ' . $number);
        $response = new VoiceResponse();

        $storeId = null;
        $activeNumber = TwilioActiveNumber::where('phone_number', '+' . trim($twilioNumber, '+'))->first();
        if ($activeNumber) {
            $storeId = StoreWebsiteTwilioNumber::where('twilio_active_number_id', $activeNumber->id)->first();
        }
        [$context, $object] = $this->findCustomerOrLeadOrOrderByNumber(str_replace('+', '', $number), $storeId->store_website_id ?? null);
        if (! $context) {
            $context = 'customers';
            $object = new Customer;
            $object->name = 'Customer from Call';
            $object->phone = str_replace('+', '', $number);
            $object->rating = 1;
            $object->save();
        }
        $dial = $response->dial('',
            [
                'record' => true,
                'recordingStatusCallback' => config('app.url') . '/twilio/recordingStatusCallback?context=' . $context . '&amp;internalId=' . $object->id,

            ]);

        $clients = $this->getConnectedClients();

        /** @var Helpers $client */
        foreach ($clients as $client) {
            $dial->client($client)->parameter([
                'name' => 'phone',
                'value' => $request->get('To'),
            ]);
        }

        return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Incoming IVR
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio/ivr")
     *
     * @uses Log
     * @uses Twiml
     * @uses Config
     *
     * @todo Can move $response code to model for Twiml object
     */
    public function ivr(Request $request)
    {
        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);

        $number = $request->get('From');
        $twilioNumber = $request->get('To');
        $call_sid = $request->get('CallSid');
        $account_sid = $request->get('AccountSid');
        // Create Twilio Log
        $inputArray = $request->input();
        $this->createTwilioLog($request, $inputArray, 'Call received from ' . $number);
        //TwilioLog::create(['log'=>'Call received from '. $number .', call sid is '. $call_sid . ' on IVR, Request is: '.json_encode($request->input()), 'account_sid'=> $account_sid,'call_sid'=>$call_sid, 'phone'=>$number]);
        //Log::channel('customerDnd')->info('Showing user profile for IVR: ');
        $count = $request->get('count');
        $call_from_enqueue = ($request->get('call_from_enqueue') != null ? $request->get('call_from_enqueue') : 0);
        $call_with_agent = ($request->get('call_with_agent') != null ? $request->get('call_with_agent') : 0);

        TwilioLog::create(['log' => 'After call received, from Mobile No ' . $number . ' Call with Agent is :' . $call_with_agent . ' and Call from enqueue is :' . $call_from_enqueue, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
        TwilioCallJourney::create(['account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number, 'call_entered' => 1]);
        //Log::channel('customerDnd')->info('call_with_agent:'.$call_with_agent);
        $storeId = null;
        $activeNumber = TwilioActiveNumber::where('phone_number', '+' . trim($twilioNumber, '+'))->first();
        if ($activeNumber) {
            $storeId = StoreWebsiteTwilioNumber::where('twilio_active_number_id', $activeNumber->id)->first();
        }
        // $this->findCustomerOrLeadOrOrderByNumber(str_replace('+', '', $number), $storeId->store_website_id ?? null);

        [$context, $object] = $this->findCustomerOrLeadOrOrderByNumber(str_replace('+', '', $number), $storeId->store_website_id ?? null);

        $this->createTwilioLog($request, $inputArray, 'log ' . $object);
        //TwilioLog::create(['log'=>'log '. $object, 'account_sid'=> $account_sid,'call_sid'=>$call_sid, 'phone'=>$number]);

        Log::channel('customerDnd')->info('object:: ' . $object);

        $store_website_id = (isset($object->store_website_id) ? $object->store_website_id : 0);

        $messageTones = TwilioMessageTone::where('store_website_id', $store_website_id)->first();

        $checkEndworkRingExist = $checkIntroRingExist = $checkBusyRingExist = false;

        if (isset($messageTones['end_work_ring']) and $messageTones['end_work_ring'] != null) {
            $endworkRing = url('twilio/' . rawurlencode($messageTones['end_work_ring']));
            $checkEndworkRingExist = $this->checkUrlExists($endworkRing); // Check URL exist or not
        }

        if (isset($messageTones['intro_ring']) and $messageTones['intro_ring'] != null) {
            $introRing = url('twilio/' . rawurlencode($messageTones['intro_ring']));
            $checkIntroRingExist = $this->checkUrlExists($introRing); // Check URL exist or not
        }

        if (isset($messageTones['busy_ring']) and $messageTones['busy_ring'] != null) {
            $busyRing = url('twilio/' . rawurlencode($messageTones['busy_ring']));
            $checkBusyRingExist = $this->checkUrlExists($busyRing); // Check URL exist or not
        }

        // if end work ring is not found take default one
        if ($checkEndworkRingExist == false) {
            $endworkRing = 'https://' . $request->getHost() . '/end_work_ring.mp3';
        }

        // if intro ring is not found take default one
        if ($checkIntroRingExist == false) {
            $introRing = 'https://' . $request->getHost() . '/intro_ring.mp3';
        }

        // if busy ring is not found take default one
        if ($checkBusyRingExist == false) {
            $busyRing = 'https://' . $request->getHost() . '/busy_ring.mp3';
        }

        $welcomeMessage = StoreWebsite::where('id', $store_website_id)->pluck('twilio_greeting_message')->first();
        if ($welcomeMessage == null) {
            $welcomeMessage = 'Welcome';
        }

        TwilioLog::create(['log' => 'Store website for mobile number ' . $number . ' is : ' . $store_website_id, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

        //Log::channel('customerDnd')->info('store_website_id: '.$store_website_id);

        $storewebsitetwiliono = StoreWebsiteTwilioNumber::where('store_website_id', '=', $store_website_id)->get();

        $twilio_active_number = [];
        if (! empty($storewebsitetwiliono) && in_array('storewebsitetwiliono', $conditions)) {
            foreach ($storewebsitetwiliono as $val) {
                $twilio_active_number[$val->id] = $val->twilio_active_number_id;
            }
        }

        $twilio_number_site_wise = implode(',', $twilio_active_number);

        if ($twilio_number_site_wise != '' && in_array('twilio_number_site_wise', $conditions)) {
            $get_numbers = TwilioActiveNumber::select('phone_number')->whereIn('id', $twilio_active_number)->get();
        } else {
            $get_numbers = TwilioActiveNumber::select('phone_number')->where('status', 'in-use')->get();
        }

        TwilioLog::create(['log' => 'After checking the store website for mobile number ' . $number . ', Twilio Active number is searched, Number From is: ' . $request->get('Called'), 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

        Log::channel('customerDnd')->info(' Number From :: >> ' . $request->get('Called'));
        $call_from = TwilioActiveNumber::where('phone_number', $request->get('Called'))->first();

        if ($call_from && in_array('call_from', $conditions)) {
            $storewebsitetwiliono_data = StoreWebsiteTwilioNumber::where('twilio_active_number_id', '=', $call_from->id)->first();
        } else {
            $storewebsitetwiliono_data = [];
        }

        // Create Twilio Log
        $inputArray = $storewebsitetwiliono_data;
        $this->createTwilioLog($request, $inputArray, 'Checked Store Website Twilio Number data for mobile number ' . $number);
        //TwilioLog::create(['log'=>' Checked Store Website Twilio Number data for mobile number '.$number.' is '.json_encode($storewebsitetwiliono_data), 'account_sid'=> $account_sid,'call_sid'=>$call_sid, 'phone'=>$number]);

        //Log::channel('customerDnd')->info(' storewebsitetwiliono_data :: >> '.json_encode($storewebsitetwiliono_data));
        // foreach ($get_numbers as $num) {
        //     Log::channel('customerDnd')->info(' Number >> '.$num['phone_number']);
        // }

        // $get_twilio_phoneno =
        // $url = \Config::get("app.url") . "/twilio/recordingStatusCallback";
        $url = 'https://' . $request->getHost() . '/twilio/recordingStatusCallback';
        // $actionurl = \Config::get("app.url") . "/twilio/handleDialCallStatus";
        $actionurl = 'https://' . $request->getHost() . '/twilio/handleDialCallStatus';
        $recordurl = 'https://' . $request->getHost() . '/twilio/storetranscript';
        /*if ($context && $object) {
            // $url = \Config::get("app.url") . "/twilio/recordingStatusCallback?context=" . $context . "&internalId=" . $object->id . "&Mobile=" ;
            $url = 'https://'.$request->getHost() . "/twilio/recordingStatusCallback?context=" . $context . "&internalId=" . $object->id . "&Mobile=" ;
        }*/

        // $response = new Twiml();
        //Log::channel('customerDnd')->info(' context >> '.$object->is_blocked);

        if ($store_website_id != 0) {
            $time_store_web_id = $store_website_id;
        } else {
            $time_store_web_id = $storewebsitetwiliono_data->store_website_id;
        }
        //return "hi362";
        $sitewise_time = TwilioSitewiseTime::where('store_website_id', $time_store_web_id)->first();

        $time = Carbon::now();
        if ($sitewise_time && in_array('sitewise_time', $conditions)) {
            $start_time = $sitewise_time->start_time;
            $start_hrs = explode(':', $start_time);
            $end_time = $sitewise_time->end_time;
            $end_hrs = explode(':', $end_time);

            $saturday = Carbon::now()->endOfWeek()->subDay();
            $sunday = Carbon::now()->endOfWeek();
            $morning = Carbon::create($time->year, $time->month, $time->day, $start_hrs[0], $start_hrs[1], 0);
            $evening = Carbon::create($time->year, $time->month, $time->day, $end_hrs[0], $end_hrs[1], 0);
        } else {
            $morning = '';
            $evening = '';
        }

        TwilioLog::create(['log' => 'For mobile number ' . $number . ' Time Stored Web id is ' . $time_store_web_id, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

        //Log::channel('customerDnd')->info(' time_store_web_id :: >> '.$time_store_web_id);
        $key_data = TwilioKeyOption::where('website_store_id', $time_store_web_id)->orderBy('key', 'ASC')->get();

        $key_wise_option = [];

        if ($key_data) {
            foreach ($key_data as $kk => $vv) {
                $key_wise_option[$vv->description]['key'] = $vv['key'];
                $key_wise_option[$vv->description]['description'] = $vv['description'];
            }
        }

        // Create Twilio Log
        $inputArray = $storewebsitetwiliono_data;
        $this->createTwilioLog($request, $inputArray, 'For mobile number ' . $number . ' Key Wise Option is ');
        //TwilioLog::create(['log'=>'For mobile number '.$number.' Key Wise Option is '.json_encode($key_wise_option), 'account_sid'=> $account_sid,'call_sid'=>$call_sid, 'phone'=>$number]);

        //Log::channel('customerDnd')->info(' key_wise_option :: >> '.json_encode($key_wise_option));

        $response = new VoiceResponse();

        $customerInfo = Customer::where('phone', str_replace('+', '', $request->get('Caller')))->first();
        $twilioCredentials = TwilioCredential::where('account_id', $request->get('AccountSid'))->first();
        $twilioActive = TwilioActiveNumber::where('phone_number', $request->get('Called'))->first();

        TwilioCallStatistic::updateOrCreate([
            'call_sid' => $request->get('CallSid'),
        ], [
            'account_sid' => $request->get('AccountSid'),
            'customer_id' => $customerInfo->id ?? null,
            'customer_website_id' => $customerInfo->store_website_id ?? null,
            'twilio_credentials_id' => $twilioCredentials->id ?? null,
            'twilio_number_website_id' => $twilioActive->assigned_stores->store_website_id ?? null,
            'customer_number' => $request->get('Caller'),
            'twilio_number' => $request->get('Called'),
            'customer_country' => $request->get('CallerCountry'),
            'twilio_number_country' => $request->get('CalledCountry'),
            'direction' => TwilioCallStatistic::DIRECTION[$request->get('Direction')],
        ]);

        // $saturday = Carbon::now()->endOfWeek()->subDay();
        // $sunday = Carbon::now()->endOfWeek();
        // $morning = Carbon::create($time->year, $time->month, $time->day, 9, 0, 0);
        // $evening = Carbon::create($time->year, $time->month, $time->day, 17, 30, 0);
        /*if (($context == "customers" && $object && $object->is_blocked == 1) || Setting::get('disable_twilio') == 1) {
           $response = $response->reject();
        } else {*/
        // if ($time == $sunday || $time == $saturday) { // If Sunday or Holiday
        //     $response->play(\Config::get("app.url") . "holiday_ring.mp3");
        // } elseif (!$time->between($morning, $evening, true)) {
        //     $response->play(\Config::get("app.url") . "end_work_ring.mp3");
        // } else {

        if ($call_with_agent == 1 && in_array('call_with_agent', $conditions)) {
            TwilioLog::create(['log' => ' Will check for agent for mobile number ' . $number, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

            //Log::channel('customerDnd')->info('::: Call with Agent :::');

            if (in_array('morning', $conditions) && in_array('evening', $conditions) && $morning != '' && $evening != '' && ! $time->between($morning, $evening, true)) {
                TwilioCallJourney::updateOrCreate(['account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number], ['called_in_working_hours' => 0]);
                TwilioLog::create(['log' => ' Oops call is not landed in working hours Closing the call for mobile number ' . $number, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

                //Log::channel('customerDnd')->info(' End work >> ');

                $call_history = TwilioCallData::updateOrCreate([
                    'call_sid' => ($request->get('CallSid') ?? 0),
                ], [
                    'call_sid' => ($request->get('CallSid') ?? 0),
                    'account_sid' => ($request->get('AccountSid') ?? 0),
                    'from' => ($request->get('Caller') ?? 0),
                    'to' => ($request->get('Called') ?? 0),
                    'call_data' => 'time_close',
                    'aget_user_id' => '',
                ]);

                if (isset($storewebsitetwiliono_data->end_work_message) && $storewebsitetwiliono_data->end_work_message != '') {
                    $response->Say($storewebsitetwiliono_data->end_work_message);
                } else {
                    $response->play($endworkRing);
                }

                // Leave the call and record the messsage of customer
                if ($call_from_enqueue == 1) {
                    $response->leave(); // Makes the current call leave the queue, but doesn't hang up the call. Execution proceeds with the next verb after the '<Enqueue>' verb.
                }
            } else {
                TwilioCallJourney::updateOrCreate(['account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number], ['called_in_working_hours' => 1]);

                TwilioLog::create(['log' => ' Call is landed on working hours for mobile number, ' . $number, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

                //Log::channel('customerDnd')->info(' working Hours >> ');

                // Execute this code only if call is come from enqueue verb
                if ($call_from_enqueue == 1) {
                    switch ((int) $count) {
                        case 0: // Coming from wait url
                            $response->redirect(route('waiturl', ['count' => 2], false));
                            break;

                        case 1: // Coming from wait url
                            TwilioLog::create(['log' => ' There is no agent is available for mobile number ' . $number, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

                            //Call History - START
                            $call_history = TwilioCallData::updateOrCreate([
                                'call_sid' => ($request->get('CallSid') ?? 0),
                            ], [
                                'call_sid' => ($request->get('CallSid') ?? 0),
                                'account_sid' => ($request->get('AccountSid') ?? 0),
                                'from' => ($request->get('Caller') ?? 0),
                                'to' => ($request->get('Called') ?? 0),
                                'call_data' => 'time_close',
                                'aget_user_id' => '',
                            ]);
                            //Call History - END

                            //Call waiting - START
                            TwilioCallWaiting::updateOrCreate([
                                'call_sid' => ($request->get('CallSid') ?? 0),
                            ], [
                                'call_sid' => ($request->get('CallSid') ?? 0),
                                'account_sid' => ($request->get('AccountSid') ?? 0),
                                'from' => ($request->get('Caller') ?? 0),
                                'to' => ($request->get('Called') ?? 0),
                                'store_website_id' => $store_website_id,
                                'status' => 0,
                            ]);
                            //Call waiting - END

                            if (isset($storewebsitetwiliono_data->message_busy) && in_array('message_busy', $conditions) && $storewebsitetwiliono_data->message_busy != '') {
                                $response->Say($storewebsitetwiliono_data->message_busy);
                            } else {
                                $response->Say($busyRing);
                            }

                            $response->leave(); // Makes the current call leave the queue, but doesn't hang up the call. Execution proceeds with the next verb after the '<Enqueue>' verb.
                            break;

                        default:
                            $response->say('Good Bye!')->leave();
                            // code...
                            break;
                    }

                    return $response;
                }

                if ($count == 2) {
                    TwilioLog::create(['log' => ' Count data is 2 for mobile number, ' . $number, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

                    // Ask customer to leave a message on beep
                    return $this->handleIncomingCall();
                }

                if ($count == 4) {
                    if (isset($storewebsitetwiliono_data->message_not_available) && $storewebsitetwiliono_data->message_not_available != '') {
                        $response->say($storewebsitetwiliono_data->message_not_available);
                    } else {
                        $response->say('Thanks you for your patience, Our All Lines are bussy. Please leave a message after the beep.');
                    }

                    $recordurl = 'https://' . $request->getHost() . '/twilio/storerecording';

                    //$response->say('Please leave a message at the beep. Press the star key when finished.');

                    $response->record(
                        [
                            'maxLength' => '20',
                            'method' => 'GET',
                            'action' => route('hangup', [], false),
                            'transcribeCallback' => $recordurl,
                            'finishOnKey' => '*',
                        ]
                    );
                    TwilioLog::create(['log' => ' Call will be hung up beacuse count data is 4 for mobile number, ' . $number, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
                    // $response->hangup();

                    return $response;
                }

                $clients = $this->getConnectedClients('customer_call_agent');
                // Create Twilio Log
                $inputArray = $clients;
                $this->createTwilioLog($request, $inputArray, 'Fetched agents data for mobile number ' . $number);
                //TwilioLog::create(['log'=>"Fetched agents data for mobile number ".$number." is ".json_encode($clients), 'account_sid'=> $account_sid,'call_sid'=>$call_sid, 'phone'=>$number]);

                $is_available = 0;
                foreach ($clients as $client) {
                    $user_details = User::find($client['agent_id']);
                    $is_online = $user_details->isOnline();

                    TwilioLog::create(['log' => 'Agent id is ' . $client['agent_id'] . ' &  Available is ' . $is_available . '  & online is ' . $is_online . ' for mobile number ' . $number, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

                    //Log::channel('customerDnd')->info('agent id >>'.$client['agent_id'].' &  is_available >>'.$is_available.'  & is_online >> '.$is_online);

                    if ($is_available == 0 && $is_online && in_array('is_available', $conditions) && in_array('is_online', $conditions)) {
                        TwilioLog::create(['log' => ' Agent Availblity before checking the call status is ' . $is_available, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

                        //Log::channel('customerDnd')->info(' client >> '.$client['agent_name_id']);

                        // Add Agent Entry - START
                        $check_agent = AgentCallStatus::where('agent_id', $client['agent_id'])->where('agent_name_id', $client['agent_name_id'])->first();
                        if ($check_agent === null && in_array('check_agent', $conditions)) {
                            // user doesn't exist in AgentCallStatus - Insert Query for Add Agent User
                            $params_insert_agent = [
                                'agent_id' => $client['agent_id'],
                                'agent_name' => $client['agent_name'],
                                'agent_name_id' => $client['agent_name_id'],
                                //'site_id' => $object->store_website_id,
                                'site_id' => $time_store_web_id,
                                'twilio_no' => $request->get('Called'),
                                'status' => '0',
                            ];
                            AgentCallStatus::create($params_insert_agent);
                        }
                        // Add Agent Entry - END

                        $check_agent_available = AgentCallStatus::where('agent_id', $client['agent_id'])->where('agent_name_id', $client['agent_name_id'])->where('twilio_no', '!=', '')->first();

                        if ($check_agent_available != null && in_array('check_agent_available', $conditions)) {
                            if ($check_agent_available->status == 0) {
                                $is_available = 1;
                            }
                        } else {
                            $is_available = 1;
                        }
                        TwilioCallJourney::updateOrCreate(['account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number], ['agent_available' => $is_available]);
                        TwilioCallJourney::updateOrCreate(['account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number], ['agent_online' => $is_online]);

                        TwilioLog::create(['log' => ' Agent Availblity after checking the call status is ' . $is_available, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

                        //Log::channel('customerDnd')->info(' is_available >> '.$is_available);

                        if ($is_available == 1 && in_array('is_available', $conditions)) {
                            TwilioLog::create(['log' => ' Assigned Agent id for mobile number ' . $number . ' is ' . $client['agent_name_id'], 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
                            $dial = $response->dial('', [
                                'record' => 'true',
                                'recordingStatusCallback' => $url,
                                'action' => $actionurl,
                                'timeout' => '60',
                            ]);

                            $dial->client($client['agent_name_id']);

                            AgentCallStatus::where('agent_id', $client['agent_id'])
                            ->where('agent_name_id', $client['agent_name_id'])
                            ->where('status', '0')
                            ->update(['status' => '1']);

                            // $call_history = TwilioCallData::create($call_history_params);
                            $call_history = TwilioCallData::updateOrCreate([
                                'call_sid' => ($request->get('CallSid') ?? 0),
                            ], [
                                'call_sid' => ($request->get('CallSid') ?? 0),
                                'account_sid' => ($request->get('AccountSid') ?? 0),
                                'from' => ($request->get('Caller') ?? 0),
                                'to' => ($request->get('Called') ?? 0),
                                'call_data' => 'client',
                                'aget_user_id' => $client['agent_id'],
                            ]);

                            TwilioCallWaiting::where('call_sid', $request->get('CallSid'))->delete();
                            //Call History - END
                        }
                    }
                }

                if ($is_available == 0 && in_array('is_available', $conditions)) {
                    //.json_encode($check_agent_available)

                    TwilioLog::create(['log' => ' There is no agent is available for mobile number ' . $number, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
                    //Log::channel('customerDnd')->info(' Not Available ---- >> ');

                    $call_history = TwilioCallData::updateOrCreate([
                        'call_sid' => ($request->get('CallSid') ?? 0),
                    ], [
                        'call_sid' => ($request->get('CallSid') ?? 0),
                        'account_sid' => ($request->get('AccountSid') ?? 0),
                        'from' => ($request->get('Caller') ?? 0),
                        'to' => ($request->get('Called') ?? 0),
                        'call_data' => 'time_close',
                        'aget_user_id' => '',
                    ]);
                    //Call History - END

                    //Call waiting - START
                    TwilioCallWaiting::updateOrCreate([
                        'call_sid' => ($request->get('CallSid') ?? 0),
                    ], [
                        'call_sid' => ($request->get('CallSid') ?? 0),
                        'account_sid' => ($request->get('AccountSid') ?? 0),
                        'from' => ($request->get('Caller') ?? 0),
                        'to' => ($request->get('Called') ?? 0),
                        'store_website_id' => $store_website_id,
                        'status' => 0,
                    ]);
                    //Call waiting - END

                    if (isset($storewebsitetwiliono_data->message_busy) && in_array('message_busy', $conditions) && $storewebsitetwiliono_data->message_busy != '') {
                        $response->Say($storewebsitetwiliono_data->message_busy);
                    } else {
                        $response->Say($busyRing);
                    }

                    $count++;

                    TwilioLog::create(['log' => 'Current waiting is ' . $count, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

                    //Log::channel('customerDnd')->info('count >> '.$count);

                    $response->redirect(route('ivr', ['call_with_agent' => 1, 'count' => $count], false));
                }
            }

            return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
        } else {
            if (isset($storewebsitetwiliono_data->message_available) && $storewebsitetwiliono_data->message_available != '') {
                $response->say($storewebsitetwiliono_data->message_available);
            } else {
                $response->play($introRing);
            }//$response->play(\Config::get("app.url") . "/intro_ring.mp3");

            $gather = $response->gather(
                [
                    'input' => 'speech dtmf',
                    'numDigits' => 1,
                    'action' => route('twilio_call_menu_response', [], false),
                ]
            );
            $categories = ReplyCategory::where('parent_id', 51)->pluck('name')->toArray();

            if (isset($storewebsitetwiliono_data->category_menu_message) && $storewebsitetwiliono_data->category_menu_message != '') {
                $in_message = $storewebsitetwiliono_data->category_menu_message;
            } else {
                $in_message = 'Hello Please Speak any keyword on which you need information like Delivery or Shipping to hear 
				more options about it listen to the following options  ';
            }

            $in_message .= implode('  ', $categories);
            if ($key_data) {
                foreach ($key_data as $kk => $vv) {
                    $in_message .= ', Please Press ' . $vv['key'] . ' for a ' . $vv['details'] . ' . ';
                }
            }
            $in_message .= ', Please Press 0 for a Communicate with Our Agent .';

            TwilioLog::create(['log' => ' Waiting for agents to free, new message on dtmf is ' . $in_message, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);

            //Log::channel('customerDnd')->info(' in message >> '.$in_message);

            $gather->say(
                $in_message,
                ['loop' => 3]
            );

            $response->record(
                ['maxLength' => '10',
                    'method' => 'GET',
                    'action' => route('twilio_call_menu_response', [], false),
                    'transcribeCallback' => $recordurl,
                ]
            );
        }

        $aa = 0;

        if ($aa == 1) {
            if ($morning != '' && in_array('morning', $conditions) && $evening != '' && in_array('evening', $conditions) && ! $time->between($morning, $evening, true)) {
                TwilioLog::create(['log' => ' Working hours over ', 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
                //Log::channel('customerDnd')->info(' End work >> ');
                // $call_history_params = [
                //     'call_sid' => ($request->get("CallSid") ?? 0),
                //     'account_sid' => ($request->get("AccountSid") ?? 0),
                //     'from' => ($request->get("Caller") ?? 0 ),
                //     'to' => ($request->get("Called") ?? 0),
                //     'call_data' => 'time_close',
                //     'aget_user_id' => ''
                // ];

                // $call_history = TwilioCallData::create($call_history_params);

                $call_history = TwilioCallData::updateOrCreate([
                    'call_sid' => ($request->get('CallSid') ?? 0),
                ], [
                    'call_sid' => ($request->get('CallSid') ?? 0),
                    'account_sid' => ($request->get('AccountSid') ?? 0),
                    'from' => ($request->get('Caller') ?? 0),
                    'to' => ($request->get('Called') ?? 0),
                    'call_data' => 'time_close',
                    'aget_user_id' => '',
                ]);

                //Call History - END

                if (isset($storewebsitetwiliono_data->end_work_message) && in_array('end_work_message', $conditions) && $storewebsitetwiliono_data->end_work_message != '') {
                    $response->say($storewebsitetwiliono_data->end_work_message);
                } else {
                    $response->play($endworkRing);
                }

            // $response->play(\Config::get("app.url") . "end_work_ring.mp3");
            } else {
                TwilioLog::create(['log' => 'called in working hours, intro tone is being played', 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
                //Log::channel('customerDnd')->info(' working Hours >> ');
                $recordurl = 'https://' . $request->getHost() . '/twilio/storerecording';
                if ($count < 1) {
                    if (isset($storewebsitetwiliono_data->message_available) && $storewebsitetwiliono_data->message_available != '') {
                        $response->say($storewebsitetwiliono_data->message_available);
                    } else {
                        $response->play($introRing);
                    }//$response->play(\Config::get("app.url") . "/intro_ring.mp3");

                    $gather = $response->gather(
                        [
                            'input' => 'speech dtmf',
                            'numDigits' => 1,
                            'action' => route('twilio_call_menu_response', [], false),
                        ]
                    );

                    $in_message = '';
                    if ($key_data) {
                        foreach ($key_data as $kk => $vv) {
                            $in_message .= ' Please Press ' . $vv['key'] . ' for a ' . $vv['details'];
                        }
                    }
                    $in_message .= ' Please Press 0 for a Communicate with Our Agent';

                    TwilioLog::create(['log' => ' key options being played ' . $in_message, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
                    //Log::channel('customerDnd')->info(' in message >> '.$in_message);

                    $gather->say(
                        $in_message,
                        ['loop' => 3]
                    );

                    $response->record(
                        ['maxLength' => '10',
                            'method' => 'GET',
                            'action' => route('twilio_call_menu_response', [], false),
                            'transcribeCallback' => $recordurl,
                        ]
                    );
                }

                if ($count == 2) {
                    $gather = $response->gather(
                        [
                            'input' => 'speech dtmf',
                            'numDigits' => 1,
                            'action' => route('twilio_menu_response', [], false),
                        ]
                    );

                    $gather->say(
                        'Currently All Lines are busy.' .
                        'Please press 1 for a leave a message. Press 2 for a ' .
                        'Hold a Call response.',
                        ['loop' => 3]
                    );
                    $response->record(
                        ['maxLength' => '10',
                            'method' => 'GET',
                            'action' => route('twilio_menu_response', [], false),
                            'transcribeCallback' => $recordurl,
                        ]
                    );
                }

                if ($count == 4) {
                    if (isset($storewebsitetwiliono_data->message_not_available) && in_array('message_not_available', $conditions) && $storewebsitetwiliono_data->message_not_available != '') {
                        $response->say($storewebsitetwiliono_data->message_not_available);
                    } else {
                        $response->say('Thanks for your patience, Our All Lines are bussy. Please leave a message');
                    }

                    // $recordurl = \Config::get("app.url") . "/twilio/storerecording";
                    $recordurl = 'https://' . $request->getHost() . '/twilio/storerecording';

                    $response->say('Please leave a message at the beep. Press the star key when finished.');

                    $response->record(
                        ['maxLength' => '20',
                            'method' => 'GET',
                            'action' => route('hangup', [], false),
                            'transcribeCallback' => $recordurl,
                            'finishOnKey' => '*',
                        ]
                    );

                    // $response->Say(
                    //     'No recording received. Goodbye',
                    //     ['voice' => 'alice', 'language' => 'en-GB']
                    // );
                    $response->hangup();

                    return $response;
                }

                $clients = $this->getConnectedClients('customer_call_agent');

                // Log::channel('customerDnd')->info('Client for callings: ' . implode(',', $clients));
                // Create Twilio Log
                $inputArray = $clients;
                $this->createTwilioLog($request, $inputArray, '');
                //TwilioLog::create(['log'=>json_encode($clients)]);
                /** @var Helpers $client */
                $is_available = 0;
                foreach ($clients as $client) {
                    $user_details = User::find($client['agent_id']);
                    $is_online = $user_details->isOnline();
                    // dd($is_online);

                    if ($is_available == 0 && $is_online && in_array('is_available', $conditions) && in_array('is_online', $conditions)) {
                        //TwilioLog::create(['log'=>' client >> '.$client['agent_name_id'], 'account_sid'=> $account_sid,'call_sid'=>$call_sid, 'phone'=>$number]);
                        //Log::channel('customerDnd')->info(' client >> '.$client['agent_name_id']);

                        // Add Agent Entry - START
                        $check_agent = AgentCallStatus::where('agent_id', $client['agent_id'])->where('agent_name_id', $client['agent_name_id'])->first();
                        if ($check_agent === null) {
                            // user doesn't exist in AgentCallStatus - Insert Query for Add Agent User
                            $params_insert_agent = [
                                'agent_id' => $client['agent_id'],
                                'agent_name' => $client['agent_name'],
                                'agent_name_id' => $client['agent_name_id'],
                                //'site_id' => $object->store_website_id,
                                'site_id' => $time_store_web_id,
                                'twilio_no' => $request->get('Called'),
                                'status' => '0',
                            ];
                            AgentCallStatus::create($params_insert_agent);
                        }
                        // Add Agent Entry - END

                        $check_agent_available = AgentCallStatus::where('agent_id', $client['agent_id'])->where('agent_name_id', $client['agent_name_id'])->where('twilio_no', '!=', '')->first();

                        if ($check_agent_available != null && in_array('check_agent_available', $conditions)) {
                            if ($check_agent_available->status == 0) {
                                $is_available = 1;
                            }
                        } else {
                            $is_available = 1;
                        }

                        TwilioLog::create(['log' => ' is agent available => ' . $is_available, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
                        //Log::channel('customerDnd')->info(' is_available >> '.$is_available);

                        if ($is_available == 1 && in_array('is_available', $conditions)) {
                            $dial = $response->dial('', [
                                'record' => 'true',
                                'recordingStatusCallback' => $url,
                                'action' => $actionurl,
                                'timeout' => '60',
                            ]);

                            $dial->client($client['agent_name_id']);

                            AgentCallStatus::where('agent_id', $client['agent_id'])
                            ->where('agent_name_id', $client['agent_name_id'])
                            ->where('status', '0')
                            ->update(['status' => '1']);

                            //Call History - START
                            // $call_history_params = [
                            //     'call_sid' => ($request->get("CallSid") ?? 0),
                            //     'account_sid' => ($request->get("AccountSid") ?? 0),
                            //     'from' => ($request->get("Caller") ?? 0 ),
                            //     'to' => ($request->get("Called") ?? 0),
                            //     'call_data' => 'client',
                            //     'aget_user_id' => $client['agent_id']
                            // ];

                            // $call_history = TwilioCallData::create($call_history_params);
                            $call_history = TwilioCallData::updateOrCreate([
                                'call_sid' => ($request->get('CallSid') ?? 0),
                            ], [
                                'call_sid' => ($request->get('CallSid') ?? 0),
                                'account_sid' => ($request->get('AccountSid') ?? 0),
                                'from' => ($request->get('Caller') ?? 0),
                                'to' => ($request->get('Called') ?? 0),
                                'call_data' => 'client',
                                'aget_user_id' => $client['agent_id'],
                            ]);
                            TwilioCallJourney::updateOrCreate(['account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number], ['call_answered' => 1]);

                            //Call History - END
                        }
                    }
                }

                if ($is_available == 0 && in_array('is_available', $conditions)) {
                    TwilioLog::create(['log' => ' Agent not available ', 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
                    //Log::channel('customerDnd')->info(' Not Available ---- >> ');
                    // $call_history_params = [
                    //     'call_sid' => ($request->get("CallSid") ?? 0),
                    //     'account_sid' => ($request->get("AccountSid") ?? 0),
                    //     'from' => ($request->get("Caller") ?? 0 ),
                    //     'to' => ($request->get("Called") ?? 0),
                    //     'call_data' => 'time_close',
                    //     'aget_user_id' => ''
                    // ];

                    // $call_history = TwilioCallData::create($call_history_params);
                    $call_history = TwilioCallData::updateOrCreate([
                        'call_sid' => ($request->get('CallSid') ?? 0),
                    ], [
                        'call_sid' => ($request->get('CallSid') ?? 0),
                        'account_sid' => ($request->get('AccountSid') ?? 0),
                        'from' => ($request->get('Caller') ?? 0),
                        'to' => ($request->get('Called') ?? 0),
                        'call_data' => 'time_close',
                        'aget_user_id' => '',
                    ]);
                    //Call History - END

                    //Call waiting - START
                    TwilioCallWaiting::updateOrCreate([
                        'call_sid' => ($request->get('CallSid') ?? 0),
                    ], [
                        'call_sid' => ($request->get('CallSid') ?? 0),
                        'account_sid' => ($request->get('AccountSid') ?? 0),
                        'from' => ($request->get('Caller') ?? 0),
                        'to' => ($request->get('Called') ?? 0),
                        'store_website_id' => $store_website_id,
                        'status' => 0,
                    ]);
                    //Call waiting - END

                    if (isset($storewebsitetwiliono_data->message_busy) && in_array('message_busy', $conditions) && $storewebsitetwiliono_data->message_busy != '') {
                        $response->Say($storewebsitetwiliono_data->message_busy);
                    } else {
                        $response->Say($welcomeMessage);
                    }

                    // $dial = $response->dial('',[
                    //     'record' => 'true',
                    //     'recordingStatusCallback' => $url,
                    //     'action' => $actionurl,
                    //     'timeout' => '60'
                    // ]);

                    // $dial->client($client['agent_name_id']);

                    $count++;
                    TwilioLog::create(['log' => 'connecting with agent attempt ' . $count, 'account_sid' => $account_sid, 'call_sid' => $call_sid, 'phone' => $number]);
                    //Log::channel('customerDnd')->info('count >> '.$count);

                    $response->redirect(route('ivr', ['count' => $count], false));
                }
            }
        }

        return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }

    public function webhookError(Request $request)
    {
        if (! empty($request->all())) {
            TwilioWebhookError::create([
                'sid' => isset($request['Sid']) ? $request->get('Sid') : '',
                'account_sid' => isset($request['AccountSid']) ? $request->get('AccountSid') : '',
                'parent_account_sid' => isset($request['ParentAccountSid']) ? $request->get('ParentAccountSid') : '',
                'level' => isset($request['Level']) ? $request->get('Level') : '',
                'payload_type' => isset($request['PayloadType']) ? $request->get('PayloadType') : '',
                'payload' => isset($request['Payload']) ? $request->get('Payload') : '',
                'timestamp' => isset($request['Timestamp']) ? Carbon::createFromTimestamp($request->get('Timestamp'))->toDateTimeString() : Carbon::now()->toDateTimeString(),
            ]);
        }
    }

    public function twilioWebhookErrorLogs()
    {
        $logs = TwilioWebhookError::paginate(100);

        return view('twilio.webhook-error-logs', compact('logs'));
    }

    // IVR Menu key input Action - START
    public function twilio_menu_response(Request $request)
    {
        $response = new VoiceResponse();
        $inputs = $request->input();
        if (isset($inputs['Digits'])) {
            $selectedOption = $request->input('Digits');
            Log::channel('customerDnd')->info('twilio_menu_response...' . $selectedOption);
            if ($selectedOption == 1) {
                // $recordurl = \Config::get("app.url") . "/twilio/storerecording";
                $recordurl = 'https://' . $request->getHost() . '/twilio/storerecording';

                $response->say('Please leave a message after the beep. Press the star key when finished.');

                $response->record(
                    ['maxLength' => '20',
                        'method' => 'POST',
                        'action' => route('waiturl', ['count' => 5], false),
                        'transcribeCallback' => $recordurl,
                        'finishOnKey' => '*',
                    ]
                );

                // $response->Say(
                //     'No recording received. Goodbye',
                //     ['voice' => 'alice', 'language' => 'en-GB']
                // );
                // $response->redirect(route('waiturl', ['count'=>5], false));

                return $response;
            } else {
                $response->say('Invalid number entered!');

                $response->redirect(route('handleIncomingCall', [], false));

                return $response;
            }
        } else {
            $recordedText = '';
            if (isset($inputs['SpeechResult'])) {
                $recordedText = $inputs['SpeechResult'];
            } else {
                if (isset($inputs['RecordingUrl'])) {
                    $recUrl = $inputs['RecordingUrl'];
                    $recordedText = (new CallBusyMessage)->convertSpeechToText($recUrl, 0, $inputs['Called'], $inputs['Caller']);
                }
            }

            $reply = ChatbotQuestion::where('value', 'like', '%' . $recordedText . '%')->orWhere('value', 'like', '%' . str_replace(' ', '_', $recordedText) . '%')->pluck('suggested_reply')->first();
            $response = new VoiceResponse();
            if ($reply == '' || $reply == null) {
                $response->Say(
                    'Invalid number entered!',
                    ['voice' => 'alice', 'language' => 'en-GB']
                );
            } else {
                $response->Say(
                    str_replace('_', ' ', $reply),
                    ['voice' => 'alice', 'language' => 'en-GB']
                );
            }

            TwilioLog::create(
                ['log' => 'User in Twilio Menu Respone, Speech Data is - ' . $recordedText . '<br> Response Data is - ' . $reply, 'account_sid' => ($request->input('AccountSid') ?? 0), 'call_sid' => ($request->input('CallSid') ?? 0), 'phone' => ($request->input('From') ?? 0), 'type' => 'speech']
            );

            $response->say(
                'Returning to the main menu',
                ['voice' => 'Alice', 'language' => 'en-GB']
            );
            $response->redirect(route('ivr', [], false));

            return $response;
        }

        $response->say(
            'Returning to the main menu',
            ['voice' => 'Alice', 'language' => 'en-GB']
        );
        $response->redirect(route('ivr', [], false));

        return $response;
    }

    public function workspaceEvent(Request $request)
    {
        // Create Twilio Log
        $inputArray = $request->all();
        $this->createTwilioLog($request, $inputArray, 'Event ');
        //TwilioLog::create(['log' => 'Event ' . json_encode($request->all()), 'account_sid'=>0 , 'call_sid'=>0 , 'phone'=>0 ]);

        $cred = TwilioCredential::where('account_id', $request->get('AccountSid'))->first();

        $twilio = new Client($cred->account_id, $cred->auth_token);
        $activity = clone $twilio;
        $task = clone $twilio;
        $call = clone $twilio;

        if ($request->get('EventType') == 'reservation.created') {
            $workerAttributes = json_decode($request->get('WorkerAttributes'));
            $agentId = str_replace('client:customer_call_agent_', '', $workerAttributes->contact_uri);

            TwilioLog::create(['log' => 'Reservation Created for Agent ' . $agentId, 'account_sid' => 0, 'call_sid' => 0, 'phone' => 0]);
            TwilioDequeueCall::updateOrCreate([
                'agent_id' => $agentId,
            ], [
                'account_sid' => $request->get('AccountSid'),
                'workspace_sid' => $request->get('WorkspaceSid'),
                'taskqueue_sid' => $request->get('TaskQueueSid'),
                'worker_sid' => $request->get('WorkerSid'),
                'task_sid' => $request->get('TaskSid'),
                'reservation_sid' => $request->get('ReservationSid'),
                'call_sid' => json_decode($request->get('TaskAttributes'))->call_sid,
                'caller' => json_decode($request->get('TaskAttributes'))->caller,
                'called' => json_decode($request->get('TaskAttributes'))->called,
            ]);

            $taskAttributes = json_decode($request->get('TaskAttributes'));

            TwilioCallData::updateOrCreate([
                'call_sid' => ($taskAttributes->call_sid ?? 0),
            ], [
                'call_sid' => ($taskAttributes->call_sid ?? 0),
                'account_sid' => ($taskAttributes->account_sid ?? 0),
                'from' => ($taskAttributes->caller ?? 0),
                'to' => ($taskAttributes->called ?? 0),
                'call_data' => 'client',
                'aget_user_id' => str_replace('client:customer_call_agent_', '', $workerAttributes->contact_uri),
            ]);
        }

        if ($request->get('EventType') == 'reservation.accepted') {
            TwilioCallWaiting::where('call_sid', json_decode($request->get('TaskAttributes'))->call_sid)->delete();

            $activtiySid = '';
            $activities = $activity->taskrouter->v1->workspaces($request->get('WorkspaceSid'))
            ->activities
            ->read(['friendlyName' => 'Unavailable'], 1);
            foreach ($activities as $record) {
                $activtiySid = $record->sid;
            }

            $twilio->taskrouter->v1->workspaces($request->get('WorkspaceSid'))
                ->workers($request->get('WorkerSid'))
                ->update([
                    'activitySid' => $activtiySid,
                ]
                );
        }

        if ($request->get('EventType') == 'reservation.wrapup') {
            $activtiySid = '';
            $activities = $activity->taskrouter->v1->workspaces($request->get('WorkspaceSid'))
            ->activities
            ->read(['friendlyName' => 'Available'], 1);
            foreach ($activities as $record) {
                $activtiySid = $record->sid;
            }

            $twilio->taskrouter->v1->workspaces($request->get('WorkspaceSid'))
                ->workers($request->get('WorkerSid'))
                ->update([
                    'activitySid' => $activtiySid,
                ]
                );

            $task->taskrouter->v1->workspaces($request->get('WorkspaceSid'))
                    ->tasks($request->get('TaskSid'))
                    ->update([
                        'assignmentStatus' => 'completed',
                    ]);

            $call = $call->calls(json_decode($request->get('TaskAttributes'))->call_sid)->fetch();

            $callStatistic = TwilioCallStatistic::where('call_sid', $call->sid)->first();
            if ($callStatistic) {
                $callStatistic->update([
                    'call_duration' => $call->duration ?? 0,
                    'call_costing' => $call->price ?? 0,
                ]);
            }
        }

        if ($request->get('EventType') == 'reservation.timeout' || $request->get('EventType') == 'reservation.canceled') {
            $twilio->calls(json_decode($request->get('TaskAttributes'))->worker_call_sid)
               ->update([
                   'status' => 'canceled',
               ]
               );
        }

        if ($request->get('EventType') == 'task.canceled') {
            TwilioCallWaiting::where('call_sid', json_decode($request->get('TaskAttributes'))->call_sid)->delete();
            $action_url = $request->getSchemeAndHttpHost() . '/twilio/cancel-task-record';
            $recording_action_url = $request->getSchemeAndHttpHost() . '/twilio/store-cancel-task-record';

            if ($request->get('Reason') != 'hangup' && $request->get('Reason') != 'leave') {
                $task->calls(json_decode($request->get('TaskAttributes'))->call_sid)
                ->update([
                    'twiml' => '<Response>
                    <Say>Currently, we are getting too much inquiry, Please leave a message at the beep. Press the star key when finished.</Say>
                    <Record action="' . $action_url . '" recordingStatusCallback="' . $recording_action_url . '" method="POST" finishOnKey="*"/>
                    </Response>',
                ]
                );
            } else {
                CallBusyMessage::updateOrCreate([
                    'caller_sid' => json_decode($request->get('TaskAttributes'))->call_sid,
                ], [
                    'message' => 'Missed Call',
                    'twilio_call_sid' => json_decode($request->get('TaskAttributes'))->caller,
                ]);
            }

            $call = $call->calls(json_decode($request->get('TaskAttributes'))->call_sid)->fetch();

            $callStatistic = TwilioCallStatistic::where('call_sid', $call->sid)->first();
            if ($callStatistic) {
                $callStatistic->update([
                    'call_duration' => $call->duration ?? 0,
                    'call_costing' => $call->price ?? 0,
                ]);
            }
        }

        return response()->json([], 204);
    }

    public function canceldTaskRecord(Request $request)
    {
        $response = new VoiceResponse();
        $response->say('We will contact you soon, Good bye');
        $response->hangup();

        return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }

    public function storeCanceldTaskRecord(Request $request)
    {
        if (isset($request->RecordingSource) && $request->RecordingSource == 'RecordVerb') {
            $dequeue = TwilioDequeueCall::where('call_sid', $request->CallSid)->first();

            CallBusyMessage::updateOrCreate([
                'caller_sid' => $request->get('CallSid'),
            ], [
                'message' => 'Missed Call',
                'twilio_call_sid' => $dequeue->caller,
            ]);

            CallRecording::updateOrCreate([
                'callsid' => $request->get('CallSid'),
            ], [
                'recording_url' => $request->get('RecordingUrl'),
                'twilio_call_sid' => $request->get('CallSid'),
            ]);

            $cred = TwilioCredential::where('account_id', $request->get('AccountSid'))->first();
            $twilio = new Client($cred->account_id, $cred->auth_token);
            $call = $twilio->calls($request->get('CallSid'))->fetch();

            $callStatistic = TwilioCallStatistic::where('call_sid', $call->sid)->first();
            if ($callStatistic) {
                $callStatistic->update([
                    'call_duration' => $call->duration ?? 0,
                    'call_costing' => $call->price ?? 0,
                ]);
            }
        }
    }

    public function storeCompleteTaskRecord(Request $request)
    {
        if ($request->get('CallStatus') == 'completed') {
            $agentId = str_replace('client:customer_call_agent_', '', $request->get('To'));

            $dequeue = TwilioDequeueCall::where('agent_id', $agentId)->first();

            CallBusyMessage::updateOrCreate([
                'caller_sid' => $dequeue->call_sid,
            ], [
                'message' => 'Completed',
                'twilio_call_sid' => $dequeue->caller,
            ]);

            CallRecording::updateOrCreate([
                'callsid' => $dequeue->call_sid,
            ], [
                'recording_url' => $request->get('RecordingUrl'),
                'twilio_call_sid' => $dequeue->call_sid,
            ]);
        }
    }

    public function assignmentTask(Request $request)
    {
        $recording_action_url = $request->getSchemeAndHttpHost() . '/twilio/store-complete-task-record';

        // Create Twilio Log
        $inputArray = $request->all();
        $this->createTwilioLog($request, $inputArray, 'Task ');
        //TwilioLog::create(['log' => 'Task ' . json_encode($request->all()), 'account_sid'=>0 , 'call_sid'=>0 , 'phone'=>0 ]);
        $dequeueInstructionModel = new \stdClass;
        $dequeueInstructionModel->instruction = 'dequeue';
        $dequeueInstructionModel->record = 'record-from-answer';
        $dequeueInstructionModel->status_callback_url = $recording_action_url;

        $dequeueInstructionJson = json_encode($dequeueInstructionModel);

        // Create Twilio Log
        $inputArray = $dequeueInstructionModel;
        $this->createTwilioLog($request, $inputArray, 'JSON ');
        //TwilioLog::create(['log' => 'JSON ' . $dequeueInstructionJson, 'account_sid'=>0 , 'call_sid'=>0 , 'phone'=>0 ]);

        return response($dequeueInstructionJson)
            ->header('Content-Type', 'application/json');
    }

    public function updateReservationStatus(Request $request)
    {
        TwilioLog::create(['log' => 'Incoming Call Reject by Agent ' . $request->get('authid'), 'account_sid' => 0, 'call_sid' => 0, 'phone' => 0]);
        $agent = TwilioDequeueCall::where('agent_id', Auth::id())->first();
        $cred = TwilioCredential::where('account_id', $agent->account_sid)->first();

        $twilio = new Client($cred->account_id, $cred->auth_token);

        $reservation = $twilio->taskrouter->v1->workspaces($agent->workspace_sid)
                                      ->tasks($agent->task_sid)
                                      ->reservations($agent->reservation_sid)
                                      ->update([
                                          'reservationStatus' => 'rejected',
                                      ]
                                      );
        // Create Twilio Log
        $inputArray = $agent->toArray();
        $this->createTwilioLog($request, $inputArray, 'Task Reservation Rejected by ' . $reservation->workerName);
        //TwilioLog::create(['log' => "Task Reservation Rejected by ". $reservation->workerName . " >>> " . json_encode($agent->toArray()), 'account_sid'=>0 , 'call_sid'=>0 , 'phone'=>0 ]);
    }

    public function rejectIncomingCall(Request $request)
    {
        $agent = TwilioDequeueCall::where('agent_id', Auth::id())->first();
        $cred = TwilioCredential::where('account_id', $agent->account_sid)->first();

        $twilio = new Client($cred->account_id, $cred->auth_token);
        $twilio->taskrouter->v1->workspaces($agent->workspace_sid)
                                    ->tasks($agent->task_sid)
                                    ->update([
                                        'assignmentStatus' => 'canceled',
                                    ]
                                    );
    }

    public function blockIncomingCall()
    {
        $call = TwilioDequeueCall::where('agent_id', Auth::id())->first();
        $cred = TwilioCredential::where('account_id', $call->account_sid)->first();

        $customerInfo = Customer::where('phone', str_replace('+', '', $call->caller))->first();
        $twilioCredentials = TwilioCredential::where('account_id', $call->account_sid)->first();
        $twilioActive = TwilioActiveNumber::where('phone_number', $call->called)->first();

        TwilioCallBlock::create([
            'user_agent_id' => $call->agent_id,
            'customer_id' => $customerInfo->id ?? null,
            'twilio_credentials_id' => $twilioCredentials->id ?? null,
            'customer_website_id' => $customerInfo->store_website_id ?? null,
            'twilio_number_website_id' => $twilioActive->assigned_stores->store_website_id ?? null,
            'customer_number' => $call->caller ?? null,
            'twilio_number' => $call->called ?? null,
        ]);

        $twilio = new Client($cred->account_id, $cred->auth_token);

        $twilio->calls($call->call_sid)
                ->update([
                    'status' => 'completed',
                ]
                );
    }

    public function waitUrl(Request $request)
    {
        $count = $request->get('count') ?? 1;

        // Find wait url exist for store websites
        $number = $request->get('From'); // Customer Number
        $twilioNumber = $request->get('To'); // Twilio number
        $storeId = null;
        $activeNumber = TwilioActiveNumber::where('phone_number', '+' . trim($twilioNumber, '+'))->first();
        if ($activeNumber) {
            $storeId = StoreWebsiteTwilioNumber::where('twilio_active_number_id', $activeNumber->id)->first();
        }

        [$context, $object] = $this->findCustomerOrLeadOrOrderByNumber(str_replace('+', '', $number), $storeId->store_website_id ?? null);

        $storeWebsiteId = (isset($object->store_website_id) ? $object->store_website_id : 0);

        $checkWaitUrlRingExist = false;
        if ($storeWebsiteId != 0) {
            $messageTones = TwilioMessageTone::where('store_website_id', $storeWebsiteId)->first();

            if (isset($messageTones['wait_url_ring']) and $messageTones['wait_url_ring'] != null) {
                $waitUrlRing = url('twilio/' . rawurlencode($messageTones['wait_url_ring']));
                $checkWaitUrlRingExist = $this->checkUrlExists($waitUrlRing); // Check URL exist or not
            }
        }

        // if wait url ring is not found take default one
        if ($checkWaitUrlRingExist == false) {
            $waitUrlRing = $request->getSchemeAndHttpHost() . '/twilio-queue-music.mp3';
        }

        TwilioLog::create(['log' => 'WaitURL ' . $count . ' | ' . $request->get('QueueTime') . ' | ' . $request->get('AvgQueueTime') . ' | ' . $waitUrlRing, 'account_sid' => 0, 'call_sid' => 0, 'phone' => 0]);

        $recordurl = 'https://' . $request->getHost() . '/twilio/storerecording';

        $response = new VoiceResponse();
        if ($count == 2) {
            $response->say('All agents are Busy. Please wait for your turn.');
            $response->play($waitUrlRing);

            $count++;
            $response->redirect(route('waiturl', ['count' => $count], false));
        } elseif ($count == 3) {
            $response->say('All agents are Busy. Please wait for your turn.');
            $response->play($waitUrlRing);

            $count++;
            $response->redirect(route('waiturl', ['count' => $count], false));
        } elseif ($count == 4) {
            $response->play($waitUrlRing);

            $response->redirect(route('ivr', ['call_with_agent' => 1, 'count' => 1, 'call_from_enqueue' => 1], false));
        } elseif ($count == 5) {
            $response->say('Thank you for your message, We will contact you soon.');
            $response->hangup();
        } else {
            $response->say('All agents are Busy. Please wait for your turn.');
            $response->play($waitUrlRing);

            $response->redirect(route('ivr', ['call_with_agent' => 1, 'count' => 0, 'call_from_enqueue' => 1], false));
        }

        return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }

    public function twilio_call_menu_response(Request $request)
    {
        $response = new VoiceResponse();
        Log::channel('customerDnd')->info('twilio_call_menu_response...' . $request);

        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);

        $number = $request->get('From');
        $to = $request->get('To');
        $AccountSid = $request->get('AccountSid');
        $CallSid = $request->get('CallSid');

        $storeId = null;
        $activeNumber = TwilioActiveNumber::where('phone_number', '+' . trim($to, '+'))->first();
        if ($activeNumber) {
            $storeId = StoreWebsiteTwilioNumber::where('twilio_active_number_id', $activeNumber->id)->first();
        }
        [$context, $object] = $this->findCustomerOrLeadOrOrderByNumber(str_replace('+', '', $number), $storeId->store_website_id ?? null);
        $store_website_id = (isset($object->store_website_id) ? $object->store_website_id : 0);
        try {
            $call_from = TwilioActiveNumber::where('phone_number', $request->get('Called'))->first();
            $workflow = TwilioWorkflow::where('workflow_sid', $call_from->workflow_sid)->first();

            // Create Twilio Log
            $inputArray = $workflow;
            $this->createTwilioLog($request, $inputArray, 'Workflow Log ');
            //TwilioLog::create(['log'=> 'Workflow Log '. json_encode($workflow),'account_sid'=> 0,'call_sid'=> 0, 'phone'=> 0 ]);
        } catch (\Exception $e) {
            TwilioLog::create(['log' => 'Workflow Log ' . $e->getMessage(), 'account_sid' => 0, 'call_sid' => 0, 'phone' => 0]);
        }

        if ($call_from && in_array('call_from', $conditions)) {
            $storewebsitetwiliono_data = StoreWebsiteTwilioNumber::where('twilio_active_number_id', '=', $call_from->id)->first();
        } else {
            $storewebsitetwiliono_data = [];
        }

        if ($store_website_id != 0 && in_array('store_website_id', $conditions)) {
            $time_store_web_id = $store_website_id;
        } else {
            $time_store_web_id = $storewebsitetwiliono_data->store_website_id;
        }

        Log::channel('customerDnd')->info('time_store_web_id: ' . $time_store_web_id);
        $inputs = $request->input();
        TwilioCallJourney::updateOrCreate(['account_sid' => $AccountSid, 'call_sid' => $CallSid, 'phone' => $number], ['handled_by_chatbot' => 1]);

        if (isset($inputs['Digits'])) {
            $selectedOption = $request->input('Digits');
            $key_data = TwilioKeyOption::where('website_store_id', $time_store_web_id)->orderBy('key', 'ASC')->get();

            $key_wise_option = [];

            if ($key_data) {
                foreach ($key_data as $kk => $vv) {
                    $key_wise_option[$vv->key]['key'] = $vv['key'];
                    $key_wise_option[$vv->key]['description'] = $vv['description'];
                    $key_wise_option[$vv->key]['message'] = $vv['message'];
                }
            }
            Log::channel('customerDnd')->info('twilio_call_menu_response...' . $selectedOption);
            if ($selectedOption == 0) {
                // $response->redirect(route('ivr', ['call_with_agent'=>1], false));
                // return $response;
                TwilioCallWaiting::updateOrCreate([
                    'call_sid' => ($request->get('CallSid') ?? 0),
                ], [
                    'call_sid' => ($request->get('CallSid') ?? 0),
                    'account_sid' => ($request->get('AccountSid') ?? 0),
                    'from' => ($request->get('Caller') ?? 0),
                    'to' => ($request->get('Called') ?? 0),
                    'store_website_id' => $store_website_id,
                    'status' => 0,
                ]);
                $priority = 0;
                $tasknumber = 0;
                if ($object) {
                    if ($object->do_not_disturb) {
                        $priority = 0;
                        $tasknumber = -1;
                    } else {
                        $priority = $object->is_priority;
                        $tasknumber = $object->is_priority;
                    }
                }

                $task = [
                    'type' => (string) $tasknumber,
                ];
                $response->enqueue(null, ['workflowSid' => $call_from->workflow_sid, 'waitUrl' => route('waiturl', [], false)])->task(json_encode($task), ['priority' => $priority, 'timeout' => $workflow->task_timeout]);

                // This code is executed after we leave enqueue using twilio leave() function
                $response->say('Please leave a message after the beep. Press the star key when finished.');

                $response->record(
                    [
                        'maxLength' => '30',
                        'method' => 'POST',
                        'action' => route('waiturl', ['count' => 5], false),
                        'transcribeCallback' => 'https://' . $request->getHost() . '/twilio/storetranscript',
                        'finishOnKey' => '*',
                    ]
                );
                TwilioLog::create(['log' => 'Enqueue Log ' . (string) $response, 'account_sid' => 0, 'call_sid' => 0, 'phone' => 0]);

                return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
            } else {
                if (array_key_exists($selectedOption, $key_wise_option)) {
                    Log::channel('customerDnd')->info('key Description ::' . $key_wise_option[$selectedOption]['description']);

                    if ($key_wise_option[$selectedOption]['description'] == 'order') {
                        Log::channel('customerDnd')->info('twilio_call_menu_response >>> order');

                        if (isset($key_wise_option[$selectedOption]['message']) && $key_wise_option[$selectedOption]['message'] != '') {
                            $response->say($key_wise_option[$selectedOption]['message']);
                        }

                        $gather = $response->gather(
                            [
                                'numDigits' => 30,
                                // 'timeout' => 2,
                                'action' => route('twilio_order_status_and_information_on_call', [], false),
                            ]
                        );

                        $gather->say(
                            'Please Enter Your Order Id',
                            ['loop' => 3]
                        );
                    } elseif ($key_wise_option[$selectedOption]['description'] == 'product') {
                        Log::channel('customerDnd')->info('twilio_call_menu_response >>> product');

                        if (isset($key_wise_option[$selectedOption]['message']) && $key_wise_option[$selectedOption]['message'] != '') {
                            $response->say($key_wise_option[$selectedOption]['message']);
                        }
                        // $response->play('https://'.$request->getHost() . "/intro_ring.mp3");

                        $gather = $response->gather(
                            [
                                'numDigits' => 1,
                                'action' => route('twilio_call_menu_response', [], false),
                            ]
                        );

                        $in_message = 'Please Press 0 for a Communicate with Our Agent';

                        $gather->say(
                            $in_message,
                            ['loop' => 3]
                        );
                    } elseif ($key_wise_option[$selectedOption]['description'] == 'administration') {
                        Log::channel('customerDnd')->info('twilio_call_menu_response >>> Administration');

                        if (isset($key_wise_option[$selectedOption]['message']) && $key_wise_option[$selectedOption]['message'] != '') {
                            $response->say($key_wise_option[$selectedOption]['message']);
                        }
                        // $response->play('https://'.$request->getHost() . "/intro_ring.mp3");

                        $gather = $response->gather(
                            [
                                'numDigits' => 1,
                                'action' => route('twilio_call_menu_response', [], false),
                            ]
                        );

                        $in_message = 'Please Press 0 for a Communicate with Our Agent';

                        $gather->say(
                            $in_message,
                            ['loop' => 3]
                        );
                    } elseif ($key_wise_option[$selectedOption]['description'] == 'socialmedia') {
                        Log::channel('customerDnd')->info('twilio_call_menu_response >>> socialmedia');

                        if (isset($key_wise_option[$selectedOption]['message']) && $key_wise_option[$selectedOption]['message'] != '') {
                            $response->say($key_wise_option[$selectedOption]['message']);
                        }
                        // $response->play('https://'.$request->getHost() . "/intro_ring.mp3");

                        $gather = $response->gather(
                            [
                                'numDigits' => 1,
                                'action' => route('twilio_call_menu_response', [], false),
                            ]
                        );

                        $in_message = 'Please Press 0 for a Communicate with Our Agent';

                        $gather->say(
                            $in_message,
                            ['loop' => 3]
                        );
                    } elseif ($key_wise_option[$selectedOption]['description'] == 'return_refund_exchange') {
                        Log::channel('customerDnd')->info('twilio_call_menu_response >>> return_refund_exchange');

                        $gather = $response->gather(
                            [
                                'timeout' => 2,
                                'action' => route('twilio_return_refund_exchange_on_call', [], false),
                            ]
                        );

                        $gather->say(
                            'Please Press 1 for Return, Please Press 2 for Refund, Please Press 3 for Exchange, Please Press 0 for a Communicate with Our Agent',
                            ['loop' => 3]
                        );
                    } elseif ($key_wise_option[$selectedOption]['description'] == 'general') {
                        Log::channel('customerDnd')->info('twilio_call_menu_response >>> general');

                        if (isset($key_wise_option[$selectedOption]['message']) && $key_wise_option[$selectedOption]['message'] != '') {
                            $response->say($key_wise_option[$selectedOption]['message']);
                        }
                        // $response->play('https://'.$request->getHost() . "/intro_ring.mp3");

                        $gather = $response->gather(
                            [
                                'numDigits' => 1,
                                'action' => route('twilio_call_menu_response', [], false),
                            ]
                        );

                        $in_message = 'Please Press 0 for a Communicate with Our Agent';

                        $gather->say(
                            $in_message,
                            ['loop' => 3]
                        );
                    } else {
                        Log::channel('customerDnd')->info('else >>>');
                        $response->say('Invalid number entered! Returning to main menu.');
                        $response->redirect(route('ivr', [], false));
                    }

                    return $response;
                } else {
                    Log::channel('customerDnd')->info('else >>>');
                    $response->say('Invalid number entered! Returning to main menu.');
                    $response->redirect(route('ivr', [], false));

                    return $response;
                }
            }
        } else {
            $recordurl = 'https://' . $request->getHost() . '/twilio/storetranscript';
            $customer = $object; // Customer's data
            if (isset($inputs['SpeechResult'])) {
                $recordedText = str_replace('.', '', $inputs['SpeechResult']);
            } else {
                $recUrl = $inputs['RecordingUrl'];
                //$recUrl = "https://erpdev3.theluxuryunlimited.com/audios/audio-file.flac";
                $recordedText = (new CallBusyMessage)->convertSpeechToText($recUrl, $time_store_web_id, $to, $number);
            }

            // If recorded text is not found again call ivr function
            if (empty($recordedText) || empty($customer)) {
                $response->say('Sorry your answer is not found. Returning to the main menu.');
                $response->redirect(route('ivr', ['count' => 2], false));

                return $response;
            }

            $params = [
                'number' => $customer->phone,
                'message' => $recordedText,
                'media_url' => null,
                'approved' => 0,
                'status' => 0,
                'contact_id' => null,
                'erp_user' => null,
                'supplier_id' => null,
                'task_id' => null,
                'dubizzle_id' => null,
                'vendor_id' => null,
                'customer_id' => $customer->id,
            ];

            // Store first data in chat message table
            $messageModel = ChatMessage::create($params);

            // Create auto reply message if answer found in our DB else go to watson reply
            \App\Helpers\MessageHelper::sendwatson($customer, $recordedText, null, $messageModel, $params, false);

            // take reply given to customer
            $checkReply = \App\ChatbotReply::where(['replied_chat_id' => $messageModel->id])->first();

            if (! empty($checkReply)) {
                $response->say($checkReply['answer']);
                $response->pause(['length' => 2]);

                $gather = $response->gather(
                    [
                        'input' => 'speech dtmf',
                        'numDigits' => 1,
                        'action' => route('twilio_call_menu_response', [], false),
                    ]
                );

                $gather->say(
                    'Speak any keyword for any further assistance. Press 0 For a Communicate with Our Agent or simply hang up the call',
                    ['loop' => 3]
                );

                $response->record(
                    ['maxLength' => '10',
                        'method' => 'GET',
                        'action' => route('twilio_call_menu_response', [], false),
                        'transcribeCallback' => $recordurl,
                    ]
                );
            } else {
                // call agent
                $gather = $response->gather(
                    [
                        'numDigits' => 1,
                        'action' => route('twilio_call_menu_response', [], false),
                        'finishOnKey' => '*',
                    ]
                );

                $gather->say(
                    'Please press 0 to communicate with our agent or press the star key to end the call.',
                    ['loop' => 3]
                );
            }
            /*$catId = \App\ReplyCategory::where(\DB::raw('lower(name)'), 'like', strtolower($recordedText))
                    ->orWhere(\DB::raw('lower(name)'), 'like', str_replace(' ', '_', strtolower($recordedText)))
                    ->where('parent_id', 51)
                    ->pluck('id')->first();
            $response = new VoiceResponse();

            if ($catId == '' || $catId == null) {
                $reply = \App\ReplyCategory::join('replies', 'reply_categories.id', 'replies.category_id')
                         ->leftJoin('store_websites', 'store_websites.id', 'replies.store_website_id')
                         ->where('model', 'Store Website')
                         ->where('replies.store_website_id', $store_website_id)
                         ->where(function ($query) use ($recordedText) {
                             $query->where(\DB::raw('lower(name)'), 'like', strtolower($recordedText))
                             ->orWhere(\DB::raw('lower(name)'), 'like', str_replace(' ', '_', strtolower($recordedText)));
                         })
                         ->pluck('replies.reply')->first();

                if ($reply == '' || $reply == null) {
                    if (isset($storewebsitetwiliono_data->speech_response_not_available_message) && $storewebsitetwiliono_data->speech_response_not_available_message != '') {
                        $in_message = $storewebsitetwiliono_data->speech_response_not_available_message;
                    } else {
                        $in_message = 'Invalid Input  ';
                    }

                    $response->Say(
                        $in_message.$recordedText,
                        ['voice' => 'alice', 'language' => 'en-GB']
                    );

                    ChatbotTypeErrorLog::create(
                        [
                            'store_website_id' => $storewebsitetwiliono_data->store_website_id,
                            'call_sid' => $CallSid,
                            'phone_number' => ($request->input('From') ?? 0),
                            'type_error' => $recordedText,
                            'is_active' => 1,
                        ]
                    );

                    TwilioLog::create(
                        ['log' => 'Speech - '.$recordedText.'<br> Response - Invalid input', 'account_sid' => ($request->input('AccountSid') ?? 0), 'call_sid' => ($request->input('CallSid') ?? 0), 'phone' => ($request->input('From') ?? 0), 'type' => 'speech']
                    );
                } else {
                    //$questions = ChatbotQuestion::where('category_id', $catId)->pluck()->toArray();
                    $response->Say(
                        $reply,
                        ['voice' => 'alice', 'language' => 'en-GB']
                    );
                    TwilioLog::create(
                        ['log' => 'Speech - '.$recordedText.'<br> Response - '.$reply, 'account_sid' => ($request->input('AccountSid') ?? 0), 'call_sid' => ($request->input('CallSid') ?? 0), 'phone' => ($request->input('From') ?? 0), 'type' => 'speech']
                    );
                }
            } else {
                $secondLevelCats = ReplyCategory::where('parent_id', $catId)->pluck('name')->toArray();
                if (isset($storewebsitetwiliono_data->sub_category_menu_message) && $storewebsitetwiliono_data->sub_category_menu_message != '') {
                    $in_message = $storewebsitetwiliono_data->sub_category_menu_message;
                } else {
                    $in_message = 'Please choose from below sub options  ';
                }

                $response->Say(
                    $in_message.str_replace('_', ' ', implode('  ', $secondLevelCats)),
                    ['voice' => 'alice', 'language' => 'en-GB']
                );

                // Create Twilio Log
                $inputArray = $workflow;
                $this->createTwilioLog($request, $inputArray, 'Speech - '.$recordedText.'<br> Response - ', 'speech');
                // TwilioLog::create(
                // 		['log'=>'Speech - '.$recordedText.'<br> Response - '. json_encode($secondLevelCats), 'account_sid'=> ($request->input("AccountSid") ?? 0),'call_sid'=>($request->input("CallSid") ?? 0), 'phone'=>($request->input("From") ?? 0), 'type'=>'speech']
                // 	);
            }

            TwilioLog::create(
                ['log' => 'User in Twilio Call Menu Respone, Speech - '.$recordedText.'<br> Response - '.$reply, 'account_sid' => ($request->input('AccountSid') ?? 0), 'call_sid' => ($request->input('CallSid') ?? 0), 'phone' => ($request->input('From') ?? 0), 'type' => 'speech']
            );

            $response->redirect(route('ivr', ['count' => 2], false)); */

            return $response;
        }

        $response->say(
            'Returning to the main menu',
            ['voice' => 'Alice', 'language' => 'en-GB']
        );
        $response->redirect(route('ivr', [], false));

        return $response;
    }

    public function twilio_order_status_and_information_on_call(Request $request)
    {
        $selectedOption = $request->input('Digits');
        $response = new VoiceResponse();
        Log::channel('customerDnd')->info('twilio_order_status_and_information_on_call Order Id = ' . $selectedOption);

        // $order_data = Customer::where('phone', '=', $number)->first();

        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);

        $order_data = Order::where('order_id', $selectedOption)->first();

        $option = ($request->get('sel_option') != null ? 1 : 0);

        if ($order_data && $option == 1 && in_array('order_data', $conditions)) {
            Log::channel('customerDnd')->info('Order Data Match' . json_encode($order_data));
            Log::channel('customerDnd')->info('Option :: > ' . $request->get('sel_option'));

            if ($request->get('sel_option') == 'return' || $request->get('sel_option') == 'exchange' || $request->get('sel_option') == 'refund') {
                $order_pro = OrderProduct::where('order_id', $order_data->id)->first();

                if ($order_pro && in_array('order_pro', $conditions)) {
                    Log::channel('customerDnd')->info('order_product_id ::  ' . $order_pro->id);

                    $return_exchange_pro = ReturnExchangeProduct::where('order_product_id', $order_pro->id)->first();

                    if ($return_exchange_pro && in_array('return_exchange_pro', $conditions)) {
                        Log::channel('customerDnd')->info('return_exchange_pro  return_exchange_id::  ' . $return_exchange_pro->return_exchange_id);

                        Log::channel('customerDnd')->info('return_exchange_pro  status_id::  ' . $return_exchange_pro->status_id);
                    }

                    $order_status = '';

                    if ($return_exchange_pro && in_array('return_exchange_pro', $conditions) && $return_exchange_pro->return_exchange_id != null && $return_exchange_pro->return_exchange_id != '') {
                        $return_exchange = ReturnExchange::where('id', $return_exchange_pro->return_exchange_id)->first();

                        $returnexchangestatus = ReturnExchangeStatus::where('id', $return_exchange->status)->first();

                        $order_status = $returnexchangestatus->status_name;

                        $response->say('Your Order ' . $request->get('sel_option') . ' Status is ' . $order_status);

                        $response->say('Thank you.');

                        $response->say('Do you need any futher support.');

                        $response->redirect(route('ivr', ['count' => 0], false));

                        $response->hangup();

                        return $response;
                    } elseif ($return_exchange_pro && in_array('return_exchange_pro', $conditions) && $return_exchange_pro->status_id != null && $return_exchange_pro->status_id != '') {
                        $returnexchangestatus = ReturnExchangeStatus::where('id', $return_exchange_pro->status_id)->first();

                        $order_status = $returnexchangestatus->status_name;

                        $response->say('Your Order ' . $request->get('sel_option') . ' Status is ' . $order_status);

                        $response->say('Thank you.');

                        $response->say('Do you need any futher support.');

                        $response->redirect(route('ivr', ['count' => 0], false));

                        return $response;
                    } else {
                        Log::channel('customerDnd')->info('Not Match Any Record regarding ' . $request->get('sel_option') . ' .');

                        $response->say('Not Match Any Record regarding ' . $request->get('sel_option') . ' .');

                        $response->redirect(route('ivr', ['count' => 0], false));

                        return $response;
                    }
                } else {
                    // Log::channel('customerDnd')->info('Not Match Any Record from your Input 22');
                    Log::channel('customerDnd')->info('Not Match Any Record regarding ' . $request->get('sel_option') . ' .');

                    $response->say('Not Match Any Record regarding ' . $request->get('sel_option') . ' .');

                    $response->redirect(route('ivr', ['count' => 0], false));

                    return $response;
                }
            }
        } elseif ($order_data && $option == 0 && in_array('order_data', $conditions)) {
            Log::channel('customerDnd')->info('Order Data Match' . json_encode($order_data));
            $order_status = '';
            if ($order_data->order_status_id != null) {
                $orderStatusList = OrderStatus::where('id', $order_data->order_status_id)->first();

                Log::channel('customerDnd')->info('Order Status = ' . $orderStatusList->status);
                $order_status = $orderStatusList->status;
            } elseif ($order_data->order_status != null) {
                $order_status = $order_data->order_status;
            }

            $response->say('Your Order Status is ' . $order_status);

            $response->say('Thank you.');
            $response->say('Do you need any futher support.');

            $response->redirect(route('ivr', ['count' => 0], false));

            $response->hangup();

            return $response;
        } else {
            Log::channel('customerDnd')->info('Not Match Any Record from your Input');

            $response->say('Not Match Any Record from your Input');

            $response->redirect(route('ivr', ['count' => 0], false));

            return $response;
        }
    }

    public function twilio_return_refund_exchange_on_call(Request $request)
    {
        $selectedOption = $request->input('Digits');
        $response = new VoiceResponse();
        Log::channel('customerDnd')->info('return_refund_exchange selectedOption = ' . $selectedOption);

        if ($selectedOption == 0) {
            // Call with our agent
            Log::channel('customerDnd')->info('return_refund_exchange >> Call with our agent ');
            $response->redirect(route('ivr', ['call_with_agent' => 1], false));
        } elseif ($selectedOption == 1) {
            //Return
            Log::channel('customerDnd')->info('return_refund_exchange >> Return ');

            $gather = $response->gather(
                [
                    'numDigits' => 30,
                    // 'timeout' => 2,
                    'action' => route('twilio_order_status_and_information_on_call', ['sel_option' => 'return'], false),
                ]
            );

            $gather->say(
                'Please Enter Your Order Id',
                ['loop' => 3]
            );
        } elseif ($selectedOption == 2) {
            //Refund
            Log::channel('customerDnd')->info('return_refund_exchange >> Refund ');

            $gather = $response->gather(
                [
                    'numDigits' => 30,
                    // 'timeout' => 2,
                    'action' => route('twilio_order_status_and_information_on_call', ['sel_option' => 'refund'], false),
                ]
            );

            $gather->say(
                'Please Enter Your Order Id',
                ['loop' => 3]
            );
        } elseif ($selectedOption == 3) {
            //Exchange
            Log::channel('customerDnd')->info('return_refund_exchange >> Exchange ');

            $gather = $response->gather(
                [
                    'numDigits' => 30,
                    // 'timeout' => 2,
                    'action' => route('twilio_order_status_and_information_on_call', ['sel_option' => 'exchange'], false),
                ]
            );

            $gather->say(
                'Please Enter Your Order Id',
                ['loop' => 3]
            );
        } else {
            $response->say('Invalid number entered!');

            $response->redirect(route('ivr', ['count' => 2], false));
        }

        return $response;
    }
    // IVR Menu key input Action - END

    public function leave_message_rec(Request $request)
    {
        $response = new VoiceResponse();
        Log::channel('customerDnd')->info(' leave_message_rec ');

        $response->hangup();

        return $response;
    }

    /**
     * Gather action
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio/gatherAction")
     *
     * @uses Log
     * @uses Twiml
     * @uses Config
     */
    public function gatherAction(Request $request)
    {
        // $response = new Twiml();
        $response = new VoiceResponse();
        Log::channel('customerDnd')->info(' TIME CHECKING : 2');

        $digits = trim($request->get('Digits'));
        Log::channel('customerDnd')->info(' TIME CHECKING : 3');

        $clients = [];

        $number = $request->get('From');
        Log::channel('customerDnd')->info(' TIME CHECKING : 4');

        // list($context, $object) = $this->findLeadOrOrderByNumber(str_replace("+", "", $number));
        // $recordurl = \Config::get("app.url") . "/twilio/storerecording";
        $recordurl = 'https://' . $request->getHost() . '/twilio/storerecording';
        // Log::channel('customerDnd')->info('Context: '.$context);
        Log::channel('customerDnd')->info(' TIME CHECKING : 5');

        if ($digits === '0') {
            Log::channel('customerDnd')->info(' Enterd into Leave a message section');
            $response->record(
                ['maxLength' => '20',
                    'method' => 'GET',
                    'action' => route('hangup', [], false),
                    'transcribeCallback' => $recordurl,
                ]
            );

            $response->Say(
                'No recording received. Goodbye',
                ['voice' => 'alice', 'language' => 'en-GB']
            );
            $response->hangup();

            return $response;
        } else {
            $this->createIncomingGather($request, $response, 'We did not understand that input.');
        }

        return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Outgoing call URL
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio/outgoing")
     *
     * @uses Log
     * @uses Twiml
     * @uses Config
     */
    public function outgoingCall(Request $request)
    {
        Log::channel('customerDnd')->info('Call Status: = ' . $request->get('CallStatus'));

        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);

        $number = $request->get('PhoneNumber');
        Log::channel('customerDnd')->info('Call SID: ' . $request->get('CallSid'));
        $context = $request->get('context');
        $id = $request->get('internalId');

        if ($request->get('CallNumber') != null) {
            $callFrom = $request->get('CallNumber');
        } else {
            $callFrom = \Config::get('twilio.default_caller_id');
        }

        // $actionurl = \Config::get("app.url") . "/twilio/handleOutgoingDialCallStatus" . "?phone_number=$number";
        $actionurl = 'https://' . $request->getHost() . '/twilio/handleOutgoingDialCallStatus' . "?phone_number=$number";

        Log::channel('customerDnd')->info('Outgoing call function Enter ' . $id);
        // $response = new Twiml();
        $response = new VoiceResponse();
        $response->dial($number, [
            'callerId' => $callFrom,
            'record' => 'true',
            // 'recordingStatusCallback' => \Config::get("app.url") . "/twilio/recordingStatusCallback?context=" . $context . "&internalId=" . $id . "&Mobile=" . $number,
            'recordingStatusCallback' => 'https://' . $request->getHost() . '/twilio/recordingStatusCallback?context=' . $context . '&internalId=' . $id . '&Mobile=' . $number,
            'action' => $actionurl,
        ]);

        //Change Agent Call Status - START
        Log::channel('customerDnd')->info('AuthId: ' . $request->get('AuthId'));
        $user_id = $request->get('AuthId');
        $user_data = User::find($user_id);

        $twilio_number_data = TwilioActiveNumber::where('phone_number', $callFrom)->first();

        $storewebsiteid = StoreWebsiteTwilioNumber::select('store_website_id')->where('twilio_active_number_id', '=', $twilio_number_data->id)->first();

        $store_website_id = $storewebsiteid->store_website_id;

        $agent_name_id = 'customer_call_agent_' . $user_id;

        $check_agent = AgentCallStatus::where('agent_id', $user_id)->where('agent_name_id', $agent_name_id)->first();

        if ($check_agent != null && in_array('check_agent', $conditions)) {
            AgentCallStatus::where('agent_id', $user_id)
            ->where('agent_name_id', $agent_name_id)
            ->where('status', '0')
            ->update(['status' => '1']);
        } else {
            $params_insert_agent = [
                'agent_id' => $user_data->id,
                'agent_name' => $user_data->name,
                'agent_name_id' => $agent_name_id,
                'site_id' => $store_website_id,
                'twilio_no' => $callFrom,
                'status' => '1',
            ];
            AgentCallStatus::create($params_insert_agent);
        }
        //Change Agent Call Status - END

        //Call History - START
        Log::channel('customerDnd')->info('outgoingCall :: TwilioCallData Added');
        Log::channel('customerDnd')->info($request->get('CallSid') . ' | ' . $request->get('AccountSid') . ' | ' . $callFrom . ' | ' . $number . ' | ' . $request->get('AuthId'));

        // $call_history_params = [
        //     'call_sid' => $request->get("CallSid"),
        //     'account_sid' => $request->get("AccountSid"),
        //     'from' => ($callFrom ?? 0),
        //     'to' => ($number ?? 0 ),
        //     'call_data' => 'agent',
        //     'aget_user_id' => $request->get("AuthId")
        // ];

        // $call_history = TwilioCallData::create($call_history_params);
        $call_history = TwilioCallData::updateOrCreate([
            'call_sid' => ($request->get('CallSid') ?? 0),
        ], [
            'call_sid' => ($request->get('CallSid') ?? 0),
            'account_sid' => ($request->get('AccountSid') ?? 0),
            'from' => ($callFrom ?? 0),
            'to' => ($number ?? 0),
            'call_data' => 'agent',
            'aget_user_id' => $request->get('AuthId'),
        ]);
        //Call History - END

        $params = [
            'twilio_call_sid' => $number,
            'message' => 'Missed Call',
            'caller_sid' => $request->get('CallSid'),
        ];
        Log::channel('customerDnd')->info('-----33333----- ');
        CallBusyMessage::create($params);

        // $recordurl = \Config::get("app.url") . "/twilio/storetranscript";
        $recordurl = 'https://' . $request->getHost() . '/twilio/storetranscript';
        Log::channel('customerDnd')->info('Trasncript Call back url ' . $recordurl);
        $response->record(['transcribeCallback' => $recordurl]);

        return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }

    public function change_agent_status(Request $request)
    {
        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);
        if ($request->get('status') !== null && \Auth::check()) {
            $user = \Auth::user();
            Log::channel('customerDnd')->info('change_agent_status >>>>');
            $user_id = $user->id;
            // $user_id = 6;

            $current_status = 1;
            $status = 0;
            $agent_name_id = 'customer_call_agent_' . $user_id;
            // $agent_name_id = 'customer_call_agent_6';

            $check_agent = AgentCallStatus::where('agent_id', $user_id)->where('agent_name_id', $agent_name_id)->first();
            if ($check_agent != null && in_array('check_agent', $conditions)) {
                AgentCallStatus::where('agent_id', $user_id)
                ->where('agent_name_id', $agent_name_id)
                ->where('status', $current_status)
                ->update(['status' => $status]);
            }
        } else {
            Log::channel('customerDnd')->info('change_agent_status  >>' . $request->get('authid'));
            $user_id = $request->get('authid');
            $current_status = ($request->get('status') == 1 ? 0 : 1);
            $status = $request->get('status');
            $agent_name_id = 'customer_call_agent_' . $user_id;
            $check_agent = AgentCallStatus::where('agent_id', $user_id)->where('agent_name_id', $agent_name_id)->first();
            if ($check_agent != null && in_array('check_agent', $conditions)) {
                AgentCallStatus::where('agent_id', $user_id)
                ->where('agent_name_id', $agent_name_id)
                ->where('status', $current_status)
                ->update(['status' => $status]);
            }
        }
    }

    public function addNumber(Request $request)
    {
        $agent_id = $request->get('authid');
        $number = $request->get('number');
        $status = $request->get('status');
        $add_number = \App\TwilioCurrentCall::create(['agent_id' => $agent_id, 'number' => $number, 'status' => $status]);
    }

    public function updateNumberStatus(Request $request)
    {
        $agent_id = $request->get('authid');
        $number = $request->get('number');
        $update_number = \App\TwilioCurrentCall::where(['agent_id' => $agent_id, 'number' => $number, 'status' => 1])->update(['status' => 0]);
    }

    public function removeWaitingCalls(Request $request)
    {
        TwilioCallWaiting::where('call_sid', $request->get('callSid'))->delete();
    }

    public function getWaitingCallList()
    {
        $agentId = Auth::id();
        $agentData = TwilioAgent::where('user_id', $agentId)->where('status', 1)->first();

        $waitingCalls = \App\TwilioCallWaiting::with('storeWebsite')->leftJoin('customers as c', 'c.phone', \DB::raw('REPLACE(twilio_call_waitings.from, "+", "")'))->where('c.store_website_id', $agentData->store_website_id)->orderBy('twilio_call_waitings.created_at', 'desc')
        ->select(['twilio_call_waitings.*', 'c.name', 'c.email'])->get();

        return response()->json([
            'calls' => $waitingCalls,
        ]);
    }

    public function change_agent_call_status(Request $request)
    {
        $user = \Auth::user();
        $user_id = $user->id;
        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);
        Log::channel('customerDnd')->info('change_agent_call_status  >>');
        // $user_id = $request->get("authid");
        $current_status = ($request->get('status') == 1 ? 0 : 1);
        $status = $request->get('status');
        $agent_name_id = 'customer_call_agent_' . $user_id;
        Log::channel('customerDnd')->info('agent_id >>> ' . $user_id);
        Log::channel('customerDnd')->info('agent_name_id >>> ' . $agent_name_id);
        $check_agent = AgentCallStatus::where('agent_id', $user_id)->where('agent_name_id', $agent_name_id)->first();
        Log::channel('customerDnd')->info('check_agent >>> ' . $check_agent);
        if ($check_agent != null && in_array('check_agent', $conditions)) {
            Log::channel('customerDnd')->info('id >>> ' . $check_agent->id);
            AgentCallStatus::where('agent_id', $user_id)
            ->where('agent_name_id', $agent_name_id)
            // ->where('status', $current_status)
            ->update(['status' => $status]);
        }
    }

    /**
     * @SWG\Post(
     *   path="/twilio-conference",
     *   tags={"Twilio"},
     *   summary="post twilio conference",
     *   operationId="post-twilio-conference",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * Outgoing Conference call URL
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio-conference")
     *
     * @uses Log
     * @uses Config
     */
    public function outgoingCallConference(Request $request, Response $response)
    {
        $from = $request->numbersFrom;
        $to = $request->numbers;
        $context = $request->context;
        $id = $request->id;
        $sid = \Config::get('twilio.account_sid');
        $token = \Config::get('twilio.auth_token');
        $twilio = new Client($sid, $token);

        foreach ($to as $number) {
            $participant = $twilio->conferences(\Config::get('twilio.conference_sid'))
                ->participants
                ->create($from, $number);
            $caller_sid = $participant->callSid;
            $details[] = ['number' => $number, 'sid' => $caller_sid];
        }

        // Via a request instance...
        return \Response::make($details, '200')->header('Content-Type', 'text/xml');
    }

    /**
     * @SWG\Post(
     *   path="/twilio-conference-mute",
     *   tags={"Twilio"},
     *   summary="post twilio mute conference",
     *   operationId="post-twilio-mute-conference",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * Mute Number From Conference
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio-conference-mute")
     *
     * @uses Log
     * @uses Config
     */
    public function muteConferenceNumber(Request $request)
    {
        $caller_sid = $request->sid;
        $participant = $twilio->conferences(\Config::get('twilio.conference_sid'))
            ->participants($caller_sid)
            ->update(['muted' => true]);
        // Via a request instance...
        return \Response::make('Muted SucessFully', '200')->header('Content-Type', 'text/xml');
    }

    /**
     * @SWG\Post(
     *   path="/twilio-conference-hold",
     *   tags={"Twilio"},
     *   summary="post twilio hold conference",
     *   operationId="post-twilio-hold-conference",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * Hold Number From Conference
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio-conference-hold")
     *
     * @uses Log
     * @uses Config
     */
    public function holdConferenceNUmber(Request $request)
    {
        $caller_sid = $request->sid;
        $participant = $twilio->conferences(\Config::get('twilio.conference_sid'))
            ->participants($caller_sid)
            ->update(['muted' => true]);
        // Via a request instance...
        return \Response::make('Hold SucessFully', '200')->header('Content-Type', 'text/xml');
    }

    /**
     * @SWG\Post(
     *   path="/twilio-conference-remove",
     *   tags={"Twilio"},
     *   summary="post twilio remove conference",
     *   operationId="post-twilio-remove-conference",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * Remove Number From Conference
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio-conference-remove")
     *
     * @uses Log
     * @uses Config
     */
    public function removeConferenceNumber(Request $request)
    {
        $caller_sid = $request->sid;
        $participant = $twilio->conferences(\Config::get('twilio.conference_sid'))
            ->participants($caller_sid)
            ->update(['muted' => true]);
        // Via a request instance...
        return \Response::make('Number Removed SucessFully', '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Store a new Trasnscript from call
     *
     * @param  Request  $request Request
     * @return string
     *
     * @Rest\Post("twilio/storetranscript")
     *
     * @uses Log
     * @uses CallRecording
     */
    public function storetranscript(Request $request)
    {
        Log::channel('customerDnd')->info('---------------- Enter in Function for Trasncript--------------------- ' . $request->get('CallStatus'));
        $sid = $request->get('CallSid');
        Log::channel('customerDnd')->info('TranscriptionText ' . $request->input('TranscriptionText'));

        $call_status = $request->get('CallStatus');
        if ($call_status == 'completed') {
            CallRecording::updateOrCreate([
                'callsid' => $sid,
            ], [
                'recording_url' => $request->input('RecordingUrl'),
                'twilio_call_sid' => $sid,
                'message' => $request->input('TranscriptionText'),
            ]);
        }

        return 'Ok';
    }

    /**
     * Outgoing call URL
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Get("twilio/getLeadByNumber")
     *
     * @uses Customer
     */
    public function getLeadByNumber(Request $request)
    {
        $number = $request->get('number');
        $twilioNumber = $request->get('twilio_number');
        $activeNumber = TwilioActiveNumber::where('phone_number', '+' . trim($twilioNumber, '+'))->first();
        if ($activeNumber) {
            $storeId = StoreWebsiteTwilioNumber::where('twilio_active_number_id', $activeNumber->id)->first();
        }
        [$context, $object] = $this->findCustomerAndRelationsByNumber(str_replace('+', '', $number), $storeId->store_website_id ?? null);

        if (! $context) {
            return response()->json(['found' => false, 'number' => $number]);
        }
//        if ($context == "leads") {
//            $result = ['found' => TRUE,
//                'context' => $context,
//                'name' => $object->client_name,
//                'email' => $object->email,
//                'customer_id' => \Config::get("app.url") . '/customer/' . $object->customer_id,
//                'customer_url' => route('customer.show'
//                    , $object->customer_id)];
//        } elseif ($context == "orders") {
//            $information = (new Order())->newQuery()
//                ->leftJoin("order_products as op","op.order_id","orders.id")
//                ->leftJoin("products as p","p.id","op.product_id")
//                ->leftJoin("brands as b","b.id","p.brand")
//                ->where('orders.id',$object->id)
//                ->select([\DB::raw("group_concat(b.name) as brand_name_list,p.id as product_image_id")])->first();
//            $result = ['found' => TRUE,
//                'context' => $context,
//                'order_id'=>$object->order_id,
//                'name' => $object->client_name,
//                'date' => Carbon::parse($object->order_date)->format('d-m-y'),
//                'brands' => $information->brand_name_list??'N/A',
//                'status' =>\App\Helpers\OrderHelper::getStatusNameById($object->order_status_id),
//                'site' => (isset($object->storeWebsiteOrder) && $object->storeWebsiteOrder) ?
//                    ($order->storeWebsiteOrder->storeWebsite??'N/A'):'N/A',
//                'customer_url' => route('customer.show', $object->customer_id)
//            ];
//            $imageData = Product::find($information->product_image_id);
//            dd($imageData->imageurl);
//
//        } elseif ($context == "customers") {
        $result = [
            'found' => true,
            'data' => $object,
        ];
//        }
        return response()->json($result);
    }

    /**
     * Recording status callback
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio/recordingStatusCallback")
     *
     * @return void
     *
     * @uses CallRecording
     */
    public function recordingStatusCallback(Request $request)
    {
        Log::channel('customerDnd')->info('recordingStatusCallback');
        $url = $request->get('RecordingUrl');
        $sid = $request->get('CallSid');
        $params = [
            'recording_url' => $url,
            'twilio_call_sid' => $sid,
            'callsid' => $sid,
        ];
        $context = $request->get('context');
        $internalId = $request->get('internalId');

        if ($context && $internalId) {
            if ($context == 'leads') {
                $params['lead_id'] = $internalId;
            } elseif ($context == 'orders') {
                $params['order_id'] = $internalId;
            } elseif ($context == 'customers') {
                $params['customer_id'] = $internalId;
            }
        }
        $customer_mobile = $request->get('Mobile');
        if ($customer_mobile != '') {
            $params['customer_number'] = $customer_mobile;
        }

        CallRecording::create($params);
    }

    /**
     * Get data of connected clients
     *
     * @param  Role  $role
     * @return array $clients
     *
     * @uses Helpers
     * @uses User
     *
     * @todo static user id's are passed and role is given
     */
    private function getConnectedClients($role = '')
    {
        // $hods = Helpers::getUsersByRoleName('HOD of CRM');
        // $hods = Helpers::getUsersRoleName('crm');
        $hods = User::Join('twilio_agents', 'twilio_agents.user_id', 'users.id')->where('twilio_agents.status', '1')->select('users.*')->get();
        // Create Twilio Log
        $inputData = [];
        $inputArray = $hods;
        $request = new Request();
        $this->createTwilioLog($request, $inputArray, 'Checking Agents in Connected Client Method ');
        //TwilioLog::create(['log'=>"Checking Agents in Connected Client Method ".json_encode($hods)]);
        // Log::channel('customerDnd')->info('hods:::::::::'.$hods);
        Log::channel('customerDnd')->info('getConnectedClients >> hods:::::::::');
        $andy = User::find(216);
        $yogesh = User::find(6);
        $clients = [];
        /** @var Helpers $hod */
        foreach ($hods as $hod) {
            if ($role == 'customer_call_agent') {
                $clients[$hod->id]['agent_id'] = $hod->id;
                $clients[$hod->id]['agent_name'] = $hod->name;
                $clients[$hod->id]['agent_name_id'] = 'customer_call_agent_' . $hod->id;
            } else {
                $clients[] = str_replace('-', '_', Str::slug($hod->name));
            }
        }

        if (Setting::get('incoming_calls_andy') == 1) {
            if ($role == 'customer_call_agent') {
                $clients[$andy->id]['agent_id'] = $andy->id;
                $clients[$andy->id]['agent_name'] = $andy->name;
                $clients[$andy->id]['agent_name_id'] = 'customer_call_agent_' . $andy->id;
            } else {
                $clients[] = str_replace('-', '_', Str::slug($andy->name));
            }
        }

        if (Setting::get('incoming_calls_yogesh') == 1) {
            if ($role == 'customer_call_agent') {
                $clients[$yogesh->id]['agent_id'] = $yogesh->id;
                $clients[$yogesh->id]['agent_name'] = $yogesh->name;
                $clients[$yogesh->id]['agent_name_id'] = 'customer_call_agent_' . $yogesh->id;
            // $clients[$yogesh->id]['agent_name_id'] = 'customer_call_agent_383';
            } else {
                $clients[] = str_replace('-', '_', Str::slug($yogesh->name));
            }
        }

        return $clients;
    }

    /**
     * Dial all clients
     *
     * @return void
     *
     * @uses Config
     * @uses Log
     *
     * @todo not in use currently
     */
    private function dialAllClients(Request $request, $response, $role = 'sales', $context = null, $object = null, $number = '')
    {
        // $url = \Config::get("app.url") . "/twilio/recordingStatusCallback";
        $url = 'https://' . $request->getHost() . '/twilio/recordingStatusCallback';
        // $actionurl = \Config::get("app.url") . "/twilio/handleDialCallStatus";
        $actionurl = 'https://' . $request->getHost() . '/twilio/handleDialCallStatus';
        if ($context) {
            // $url = \Config::get("app.url") . "/twilio/recordingStatusCallback?context=" . $context . "&internalId=" . $object->id . "&Mobile=" . $object->phone;
            $url = 'https://' . $request->getHost() . '/twilio/recordingStatusCallback?context=' . $context . '&internalId=' . $object->id . '&Mobile=' . $object->phone;
        }

        $dial = $response->dial([
            'record' => 'true',
            'recordingStatusCallback' => $url,
            'action' => $actionurl,
            'timeout' => 5,
        ]);

        $clients = $this->getConnectedClients($role);

        Log::channel('customerDnd')->info('Client for callings: ' . implode(',', $clients));
        /** @var Helpers $client */
        foreach ($clients as $client) {
            $dial->client($client);
        }
    }

    /**
     * Incoming calls gathering
     *
     * @param  object  $response
     *
     * @uses Config
     *
     * @return void
     */
    private function createIncomingGather(Request $request, $response, $speech)
    {
        Log::channel('customerDnd')->info('Gathering action...');

        $action_url = 'https://' . $request->getHost() . '/twilio/gatherAction';
        $gather = $response->gather([
            // 'action' => url("/twilio/gatherAction")
            'action' => $action_url,
        ]);
        // $gather->play(\Config::get("app.url") . "/busy_ring.mp3");
        $gather->play('https://' . $request->getHost() . '/busy_ring.mp3');
    }

    /**
     * Handle Dial call callback
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio/handleDialCallStatus")
     *
     * @uses CallHistory
     * @uses Customer
     * @uses Log
     */
    public function handleDialCallStatus(Request $request)
    {
        if (isset($request->CallDuration) && $request->CallDuration == 1) {
            \Cache::forever('fdfdas', $request->all());
            $request->merge(['status' => 'missed']);
            $this->eventsFromFront($request);
        }
        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);
        // $response = new Twiml();
        $response = new VoiceResponse();
        $callStatus = $request->input('DialCallStatus');
        // $recordurl = \Config::get("app.url") . "/twilio/storerecording";
        $recordurl = 'https://' . $request->getHost() . '/twilio/storerecording';
        Log::channel('customerDnd')->info('Current Call Status ' . $callStatus);

        if ($callStatus === 'completed') {
            // $recordurl = \Config::get("app.url") . "/twilio/storetranscript";
            $recordurl = 'https://' . $request->getHost() . '/twilio/storetranscript';
            Log::channel('customerDnd')->info('Trasncript Call back url ' . $recordurl);
            $response->record(['transcribeCallback' => $recordurl]);
        } else {
            $params = [
                'twilio_call_sid' => $request->input('Caller'),
                'message' => 'Missed Call',
                'caller_sid' => $request->input('CallSid'),
            ];

            //Add Customer If Not Exist in

            $check_customer = Customer::where('phone', 'LIKE', str_replace('+', '', $request->input('Caller')))->first();

            if (! $check_customer && in_array('check_customer', $conditions)) {
                $get_twilio_active_number = TwilioActiveNumber::where('phone_number', $request->input('Called'))->first();
                $store_web_twilio_no = StoreWebsiteTwilioNumber::where('twilio_active_number_id', $get_twilio_active_number->id)->first();

                $defaultWhatapp = $task_info = \DB::table('whatsapp_configs')
                ->select('*')
                    ->whereRaw('find_in_set(' . CustomerController::DEFAULT_FOR . ',default_for)')
                    ->first();
                $defaultNo = $defaultWhatapp->number;

                $add_customer = [
                    'name' => str_replace('+', '', $request->input('Caller')),
                    'phone' => str_replace('+', '', $request->input('Caller')),
                    'whatsapp_number' => $defaultNo,
                    'store_website_id' => $store_web_twilio_no->store_website_id,
                ];

                Customer::create($add_customer);
            }
            Log::channel('customerDnd')->info('-----222222----- ');

            CallBusyMessage::create($params);

            Log::channel('customerDnd')->info(' Missed Call saved');
            Log::channel('customerDnd')->info('-----SID----- ' . $request->input('CallSid'));

            $this->createIncomingGather($request, $response, 'Please dial 0 for leave message');
        }

        if ($customer = Customer::where('phone', 'LIKE', str_replace('+91', '', $request->input('Caller')))->first()) {
            $params = [
                'customer_id' => $customer->id,
                'status' => '',
            ];

            CallHistory::create($params);
        }

        return $response;
    }

    /**
     * Handle Dial call callback
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio/handleOutgoingDialCallStatus")
     *
     * @uses CallHistory
     * @uses Customer
     * @uses ChatMessage
     * @uses Log
     */
    public function handleOutgoingDialCallStatus(Request $request)
    {
        // $response = new Twiml();
        $response = new VoiceResponse();
        $callStatus = $request->input('DialCallStatus');
        Log::channel('customerDnd')->info('Current Outgoing Call Status ' . $callStatus);
        // Log::channel('customerDnd')->info($request->all());

        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);

        if ($callStatus == 'busy' || $callStatus == 'no-answer' && in_array('callStatus', $conditions)) {
            if ($customer = Customer::where('phone', $request->phone_number)->first()) {
                $params = [
                    'number' => null,
                    'message' => 'Greetings from Solo Luxury, our Solo Valets were trying to get in touch with you but were unable to get through, you can call us on 0008000401700. Please do not use +91 when calling  as it does not connect to our toll free number.',
                    'customer_id' => $customer->id,
                    'approved' => 1,
                    'status' => 2,
                ];

                ChatMessage::create($params);

                app(\App\Http\Controllers\WhatsAppController::class)->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $params['message']);
            }

            if ($request->input('From') != null || $request->input('From') != null || $request->input('From') != '') {
                $Caller = $request->input('From');
            } else {
                $Caller = $request->input('Caller');
            }

            $user_data = explode(':', $Caller);
            $user = $user_data[1];

            $current_status = 1;
            $status = 0;
            $agent_name_id = $user;
            // $agent_name_id = 'customer_call_agent_6';

            $check_agent = AgentCallStatus::where('agent_name_id', $agent_name_id)->first();
            if ($check_agent != null && in_array('check_agent', $conditions)) {
                AgentCallStatus::where('agent_name_id', $agent_name_id)
                ->where('status', $current_status)
                ->update(['status' => $status]);
            }
        }

        if ($customer = Customer::where('phone', 'LIKE', str_replace('+91', '', $request->phone_number))->first()) {
            if ($callStatus == null || $callStatus == '' && in_array('callStatus', $conditions)) {
                $callStatus = 'missed';
            }

            $params = [
                'customer_id' => $customer->id,
                'status' => $callStatus,
            ];

            CallHistory::create($params);
        }

        // $this->change_agent_status();

        return $response;
    }

    /**
     * Store a new recording from callback
     *
     * @param  Request  $request Request
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("twilio/storerecording")
     *
     * @uses CallBusyMessage
     */
    public function storeRecording(Request $request)
    {
        Log::channel('customerDnd')->info('storeRecording ');

        $url = $request->get('RecordingUrl');
        $sid = $request->get('CallSid');
        $params = [
            'recording_url' => $url,
            'twilio_call_sid' => $sid,
            'callsid' => $sid,
        ];

        CallRecording::create($params);

        Log::channel('customerDnd')->info('outgoingCall :: TwilioCallData Added');
        Log::channel('customerDnd')->info($request->get('CallSid') . ' | ' . $request->get('AccountSid') . ' | ' . $request->get('Caller') . ' | ' . $request->get('Called') . ' | ' . $request->get('AuthId'));

        // $call_history_params = [
        //     'call_sid' => $request->get("CallSid"),
        //     'account_sid' => $request->get("AccountSid"),
        //     'from' => $request->get("Caller"),
        //     'to' => $request->get("Called"),
        //     'call_data' => 'leave_message',
        //     'aget_user_id' => null
        // ];

        // $call_history = TwilioCallData::create($call_history_params);

        $call_history = TwilioCallData::updateOrCreate([
            'call_sid' => ($request->get('CallSid') ?? 0),
        ], [
            'call_sid' => ($request->get('CallSid') ?? 0),
            'account_sid' => ($request->get('AccountSid') ?? 0),
            'from' => $request->get('Caller'),
            'to' => $request->get('Called'),
            'call_data' => 'leave_message',
            'aget_user_id' => null,
        ]);

        TwilioCallWaiting::where('call_sid', $request->get('CallSid'))->delete();

        // $params = [
        //     'recording_url' => $request->input('RecordingUrl'),
        //     'twilio_call_sid' => $request->input('Caller'),
        //     'message' => $request->input('TranscriptionText')
        // ];

        // Log::channel('customerDnd')->info('storeRecording params : '.$params );

        // $exist_call = CallBusyMessage::where('caller_sid', '=', $request->input('CallSid'))->first();
        // if ($exist_call) {
        //     CallBusyMessage::where('caller_sid', $request->input('CallSid'))
        //         ->first()
        //         ->update($params);
        //     Log::channel('customerDnd')->info('update call busy recording table');
        // } else {

        //     Log::channel('customerDnd')->info('Recording URL' . $request->input('RecordingUrl'));
        //     Log::channel('customerDnd')->info('Caller NAME ' . $request->input('From'));
        //     Log::channel('customerDnd')->info('-----SID----- ' . $request->input('CallSid'));
        //     Log::channel('customerDnd')->info('-----11111----- ');
        //     CallBusyMessage::create($params);
        //     Log::channel('customerDnd')->info('insert new call busy recording table');
        // }
    }

    /**
     * Replies with a hangup
     *
     * @return \Illuminate\Http\Response
     *
     * @Rest\Post("/twilio/hangup")
     */
    public function showHangup(Request $request)
    {
        // Create Twilio Log
        $inputArray = $request->input();
        $this->createTwilioLog($request, $inputArray, 'Call is going to hungup ');
        //TwilioLog::create(['log'=>"Call is going to hungup ".json_encode($request->input())]);
        $response = new VoiceResponse();
        $response->Say(
            'Thank you for your message. Goodbye',
            ['voice' => 'alice', 'language' => 'en-GB']
        );
        $response->hangup();

        return $response;
    }

    public function manageTwilioAccounts(Request $request)
    {
        $all_accounts = TwilioCredential::where(['status' => 1])->where('twiml_app_sid', '!=', null);

        $twilio_user_list = User::LeftJoin('twilio_agents', 'user_id', 'users.id')->select('users.*', 'twilio_agents.status')->orderBy('users.name', 'ASC')->get();

        $store_websites = StoreWebsite::LeftJoin('twilio_sitewise_times as tst', 'tst.store_website_id', 'store_websites.id')->select('store_websites.*', 'tst.start_time', 'tst.end_time')->orderBy('store_websites.website', 'ASC')->get();

        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);

        $twilio_key_options_data = TwilioKeyOption::get();
        $twilio_key_arr = [];

        $twiliconditionsemails = TwilioCredential::distinct('twilio_email')->pluck('twilio_email');
        $twiliAccountIds = TwilioCredential::distinct('account_id')->pluck('account_id');
        $twiliAuthTokens = TwilioCredential::distinct('auth_token')->pluck('auth_token');

        if ($request->twilicondition_email !== null) {
            $all_accounts = $all_accounts->WhereIn('twilio_email', $request->twilicondition_email);
        }
        if ($request->account_id !== null) {
            $all_accounts = $all_accounts->WhereIn('account_id', $request->account_id);
        }
        if ($request->auth_token !== null) {
            $all_accounts = $all_accounts->WhereIn('auth_token', $request->auth_token);
        }
        if ($request->recovery_code !== null) {
            $all_accounts = $all_accounts->where('twilio_recovery_code', 'LIKE', '%' . $request->recovery_code . '%');
        }

        $all_accounts = $all_accounts->get();

        if ($twilio_key_options_data && in_array('twilio_key_options_data', $conditions)) {
            foreach ($twilio_key_options_data as $key => $value) {
                $twilio_key_arr[$value->key]['option'] = $value->description;
                $twilio_key_arr[$value->key]['desc'] = $value->details;
                $twilio_key_arr[$value->key]['message'] = $value->message;
            }
        }

        return view('twilio.manage-accounts', compact('all_accounts', 'twilio_user_list', 'store_websites', 'twilio_key_arr','twiliconditionsemails','twiliAccountIds','twiliAuthTokens'));
    }

    public function addAccount(Request $request)
    {
        try {
            if (isset($request->id)) {
                //then update

                TwilioCredential::where('id', '=', $request->id)->update([
                    'twilio_email' => $request->email,
                    'account_id' => $request->account_id,
                    'auth_token' => $request->auth_token,
                    'twilio_recovery_code' => $request->recovery_code,
                ]);

                return redirect()->back()->with('success', 'Twilio details updated successfully');
            } else {
                $create_new = TwilioCredential::create([
                    'twilio_email' => $request->email,
                    'account_id' => $request->account_id,
                    'auth_token' => $request->auth_token,
                    'twilio_recovery_code' => $request->recovery_code,
                ]);

                //Create TwiML Apps - START
                $sid = $request->account_id;
                $token = $request->auth_token;
                $twilio = new Client($sid, $token);
                // $voice_request_url = \Config::get("app.url") . "/twilio/outgoing";
                $voice_request_url = 'https://' . $request->getHost() . '/twilio/outgoing';

                $application = $twilio->applications
                ->create([
                    'voiceMethod' => 'GET',
                    'voiceUrl' => $voice_request_url,
                    'friendlyName' => 'voice call',
                ]
                );

                if ($application) {
                    TwilioCredential::where('id', '=', $create_new->id)->update(['twiml_app_sid' => $application->sid]);
                }
                //Create TwiML Apps - END

                //Get Phone Number - START
                $local = $twilio->availablePhoneNumbers('US')
                                ->local
                                ->read(['areaCode' => 510], 20);

                // $tollFree = $twilio->availablePhoneNumbers("US")
                //                 ->tollFree
                //                 ->read([], 20);

                // $mobile = $twilio->availablePhoneNumbers("GB")
                //                 ->mobile
                //                 ->read([], 20);

                $phone_number = $local[0]->phoneNumber;

                // $voice_call_comes_url = \Config::get("app.url") . "/twilio/ivr";
                // $call_status_changes_url = \Config::get("app.url") . "/twilio/handleDialCallStatus";
                $voice_call_comes_url = 'https://' . $request->getHost() . '/twilio/ivr';
                $call_status_changes_url = 'https://' . $request->getHost() . '/twilio/handleDialCallStatus';

                $incoming_phone_number = $twilio->incomingPhoneNumbers
                ->create(['phoneNumber' => $phone_number]);

                // dd($incoming_phone_number);
                // $available_phone_number_country = $twilio->availablePhoneNumbers("US")->fetch();

                // $url = 'https://api.twilio.com/2010-04-01/Accounts/'.$sid.'/AvailablePhoneNumbers/US.json';
                // $result = TwilioHelper::curlGetRequest($url, $sid, $token);
                // $result = json_decode($result);

                //Get Phone Number - END
                TwilioAccountLog::log($request->email, $request->account_id, 'New twilio account added successfully');

                return redirect()->back()->with('success', 'New twilio account added successfully');
            }
        } catch (\Exception $e) {
            TwilioAccountLog::log($request->email, $request->account_id, $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteAccount($id)
    {
        try {
            TwilioCredential::where('id', '=', $id)->update(['status' => 0]);

            return redirect()->back()->with('success', 'Twilio account deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * This function is use to getting detail for selected email address from the Twilio number
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function manageNumbersPopup($id)
    {
        try {
            $account_id = $id;
            //get account details
            $check_account = TwilioCredential::where(['id' => $id])->where('twiml_app_sid', '!=', null)->firstOrFail();
            $numbers = TwilioActiveNumber::where('twilio_credential_id', '=', $id)->with('assigned_stores.store_website')->get();
            $store_websites = StoreWebsite::all();
            $customer_role_users = RoleUser::where(['role_id' => 1])->with('user')->get();
            $workspace = TwilioWorkspace::where('twilio_credential_id', '=', $id)->where('deleted', 0)->get();
            $twilio_user_list = User::LeftJoin('twilio_agents', 'user_id', 'users.id')->select('users.*', 'twilio_agents.status')->orderBy('users.name', 'ASC')->get();
            // $worker = TwilioWorker::where('twilio_credential_id', '=', $id)->where('deleted',0)->get();

            $worker = TwilioWorker::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_workers.twilio_workspace_id')
            ->where('twilio_workers.twilio_credential_id', '=', $id)
            ->where('twilio_workers.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_workers.*')
            ->get();

            $activities = TwilioActivity::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_activities.twilio_workspace_id')
            ->where('twilio_activities.twilio_credential_id', '=', $id)
            ->where('twilio_activities.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_activities.*')
            ->get();
            foreach ($activities as $activity) {
                if ($activity['availability'] == 1) {
                    $activity['availability'] = 'True';
                } else {
                    $activity['availability'] = 'False';
                }
            }
            $workflows = TwilioWorkflow::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_workflows.twilio_workspace_id')
             ->where('twilio_workflows.twilio_credential_id', '=', $id)
             ->where('twilio_workflows.deleted', 0)
             ->select('twilio_workspaces.workspace_name', 'twilio_workflows.*')
             ->get();

            $taskqueue = TwilioTaskQueue::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_task_queue.twilio_workspace_id')
            ->where('twilio_task_queue.twilio_credential_id', '=', $id)
            ->where('twilio_task_queue.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_task_queue.*')
            ->get();

            return view('twilio.manage-numbers-list', compact('numbers', 'store_websites', 'customer_role_users', 'account_id', 'workspace', 'worker', 'activities', 'workflows', 'taskqueue', 'twilio_user_list'));
        } catch (\Exception $e) {
            return $e->getMessage(); //redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function manageNumbers($id)
    {
        try {
            $account_id = $id;
            //get account details
            $check_account = TwilioCredential::where(['id' => $id])->where('twiml_app_sid', '!=', null)->firstOrFail();
            $numbers = TwilioActiveNumber::where('twilio_credential_id', '=', $id)->with('assigned_stores.store_website')->get();
            $store_websites = StoreWebsite::all();
            $customer_role_users = RoleUser::where(['role_id' => 1])->with('user')->get();
            $workspace = TwilioWorkspace::where('twilio_credential_id', '=', $id)->where('deleted', 0)->get();

            $twilio_user_list = User::LeftJoin('twilio_agents', 'user_id', 'users.id')->select('users.*', 'twilio_agents.status')->orderBy('users.name', 'ASC')->get();
            // $worker = TwilioWorker::where('twilio_credential_id', '=', $id)->where('deleted',0)->get();

            $priority = TwilioPriority::where('twilio_priorities.account_id', '=', $id)
            ->where('twilio_priorities.deleted_at', null)
            ->select('twilio_priorities.*')
            ->orderBy('twilio_priorities.priority_no')
            ->get();

            $worker = TwilioWorker::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_workers.twilio_workspace_id')
            ->where('twilio_workers.twilio_credential_id', '=', $id)
            ->where('twilio_workers.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_workers.*')
            ->get();

            $activities = TwilioActivity::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_activities.twilio_workspace_id')
            ->where('twilio_activities.twilio_credential_id', '=', $id)
            ->where('twilio_activities.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_activities.*')
            ->get();
            foreach ($activities as $activity) {
                if ($activity['availability'] == 1) {
                    $activity['availability'] = 'True';
                } else {
                    $activity['availability'] = 'False';
                }
            }
            $workflows = TwilioWorkflow::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_workflows.twilio_workspace_id')
             ->where('twilio_workflows.twilio_credential_id', '=', $id)
             ->where('twilio_workflows.deleted', 0)
             ->select('twilio_workspaces.workspace_name', 'twilio_workflows.*')
             ->get();

            $taskqueue = TwilioTaskQueue::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_task_queue.twilio_workspace_id')
            ->where('twilio_task_queue.twilio_credential_id', '=', $id)
            ->where('twilio_task_queue.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_task_queue.*')
            ->get();

            return view('twilio.manage-numbers', compact('numbers', 'store_websites', 'customer_role_users', 'account_id', 'workspace', 'worker', 'priority', 'activities', 'workflows', 'taskqueue', 'twilio_user_list'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * THis function is use for the list messages on seperate page for each website.
     *
     * @params $id [int]
     *
     * retuen View
     */
    public function manageAllNumbers($id)
    {
        try {
            if ($id != 'none') {
                $account_id = $id;
                //get account details
                $check_account = TwilioCredential::where(['id' => $id])->where('twiml_app_sid', '!=', null)->firstOrFail();
                $numbers = TwilioActiveNumber::where('twilio_credential_id', '=', $id)->with('assigned_stores.store_website')->get();
                $store_websites = StoreWebsite::all();
                $customer_role_users = RoleUser::where(['role_id' => 1])->with('user')->get();
                $workspace = TwilioWorkspace::where('twilio_credential_id', '=', $id)->where('deleted', 0)->get();

                $twilio_user_list = User::LeftJoin('twilio_agents', 'user_id', 'users.id')->select('users.*', 'twilio_agents.status')->orderBy('users.name', 'ASC')->get();
                // $worker = TwilioWorker::where('twilio_credential_id', '=', $id)->where('deleted',0)->get();

                $priority = TwilioPriority::where('twilio_priorities.account_id', '=', $id)
                ->where('twilio_priorities.deleted_at', null)
                ->select('twilio_priorities.*')
                ->get();

                $worker = TwilioWorker::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_workers.twilio_workspace_id')
                ->where('twilio_workers.twilio_credential_id', '=', $id)
                ->where('twilio_workers.deleted', 0)
                ->select('twilio_workspaces.workspace_name', 'twilio_workers.*')
                ->get();

                $activities = TwilioActivity::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_activities.twilio_workspace_id')
                ->where('twilio_activities.twilio_credential_id', '=', $id)
                ->where('twilio_activities.deleted', 0)
                ->select('twilio_workspaces.workspace_name', 'twilio_activities.*')
                ->get();
                foreach ($activities as $activity) {
                    if ($activity['availability'] == 1) {
                        $activity['availability'] = 'True';
                    } else {
                        $activity['availability'] = 'False';
                    }
                }
                $workflows = TwilioWorkflow::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_workflows.twilio_workspace_id')
                    ->where('twilio_workflows.twilio_credential_id', '=', $id)
                    ->where('twilio_workflows.deleted', 0)
                    ->select('twilio_workspaces.workspace_name', 'twilio_workflows.*')
                    ->get();

                $taskqueue = TwilioTaskQueue::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_task_queue.twilio_workspace_id')
                ->where('twilio_task_queue.twilio_credential_id', '=', $id)
                ->where('twilio_task_queue.deleted', 0)
                ->select('twilio_workspaces.workspace_name', 'twilio_task_queue.*')
                ->get();

                return view('twilio.manage_all_numbers', compact('numbers', 'store_websites', 'customer_role_users', 'account_id', 'workspace', 'worker', 'priority', 'activities', 'workflows', 'taskqueue', 'twilio_user_list'));
            } else {
                return redirect()->route('twilio-manage-accounts')->with('error', 'Please slect from number row. ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getTwilioActiveNumbers($account_id)
    {
        try {
            //get account details
            $check_account = TwilioCredential::where(['id' => $account_id])->where('twiml_app_sid', '!=', null)->firstOrFail();
            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $check_account->account_id . '/IncomingPhoneNumbers.json?Beta=false&PageSize=50&Page=0';
            $result = TwilioHelper::curlGetRequest($url, $check_account->account_id, $check_account->auth_token);
            $result = json_decode($result);

            if (count($result->incoming_phone_numbers) > 0) {
                $this->saveNumber($result->incoming_phone_numbers, $account_id);
            }
            if ($result->end > 0) {
                for ($i = 1; $i <= $result->end; $i++) {
                    $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $check_account->account_id . '/IncomingPhoneNumbers.json?Beta=false&PageSize=50&Page=' . $i;
                    $result = TwilioHelper::curlGetRequest($url, $check_account->account_id, $check_account->auth_token);
                    $result = json_decode($result);
                    if (count($result->incoming_phone_numbers) > 0) {
                        $this->saveNumber($result->incoming_phone_numbers, $account_id);
                    }
                }
            }

            return redirect()->back()->with('success', 'Number saved successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function saveNumber($incoming_phone_numbers, $account_id)
    {
        try {
            foreach ($incoming_phone_numbers as $numbers) {
                //check if no. already exists then update
                $find_number = TwilioActiveNumber::where('phone_number', '=', $numbers->phone_number)->where('twilio_credential_id', $account_id)->first();
                if ($find_number) {
                    $find_number->update([
                        'sid' => $numbers->sid,
                        'account_sid' => $numbers->account_sid,
                        'friendly_name' => $numbers->friendly_name,
                        'phone_number' => $numbers->phone_number,
                        'voice_url' => $numbers->voice_url,
                        'date_created' => $numbers->date_created,
                        'date_updated' => $numbers->date_updated,
                        'sms_url' => $numbers->sms_url,
                        'voice_receive_mode' => isset($numbers->voice_receive_mode) ?? 'voice',
                        'api_version' => $numbers->api_version,
                        'voice_application_sid' => $numbers->voice_application_sid,
                        'sms_application_sid' => $numbers->sms_application_sid,
                        'trunk_sid' => $numbers->trunk_sid,
                        'emergency_status' => $numbers->emergency_status,
                        'emergency_address_id' => $numbers->emergency_address_sid,
                        'address_sid' => $numbers->address_sid,
                        'identity_sid' => $numbers->identity_sid,
                        'bundle_sid' => $numbers->bundle_sid,
                        'uri' => $numbers->uri,
                        'status' => $numbers->status,
                        'twilio_credential_id' => $account_id,
                    ]);
                } else {
                    TwilioActiveNumber::create([
                        'sid' => $numbers->sid,
                        'account_sid' => $numbers->account_sid,
                        'friendly_name' => $numbers->friendly_name,
                        'phone_number' => $numbers->phone_number,
                        'voice_url' => $numbers->voice_url,
                        'date_created' => $numbers->date_created,
                        'date_updated' => $numbers->date_updated,
                        'sms_url' => $numbers->sms_url,
                        'voice_receive_mode' => isset($numbers->voice_receive_mode) ?? 'voice',
                        'api_version' => $numbers->api_version,
                        'voice_application_sid' => $numbers->voice_application_sid,
                        'sms_application_sid' => $numbers->sms_application_sid,
                        'trunk_sid' => $numbers->trunk_sid,
                        'emergency_status' => $numbers->emergency_status,
                        'emergency_address_id' => $numbers->emergency_address_sid,
                        'address_sid' => $numbers->address_sid,
                        'identity_sid' => $numbers->identity_sid,
                        'bundle_sid' => $numbers->bundle_sid,
                        'uri' => $numbers->uri,
                        'status' => $numbers->status,
                        'twilio_credential_id' => $account_id,
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getWorkflowList(Request $request)
    {
        try {
            $workspace = TwilioWorkspace::where('workspace_sid', $request->workspace_sid)->firstOrFail();

            $workflows = TwilioWorkflow::where('twilio_workspace_id', $workspace->id)->where('deleted', 0)->get();

            return response()->json([
                'workflows' => $workflows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Please select valid Workflow',
            ], 500);
        }
    }

    public function assignTwilioNumberToStoreWebsite(Request $request)
    {
        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);
        //check if same no. assigned to some store website
        try {
            // StoreWebsiteTwilioNumber::where('twilio_active_number_id', '=', $request->twilio_number_id)
            //                         ->where('store_website_id','!=',$request->store_website_id)->firstOrFail();

            // return new JsonResponse(['status' => 0, 'message' => 'Number already assigned to another site']);

            $check_rec = StoreWebsiteTwilioNumber::where('store_website_id', '=', $request->store_website_id)
                                    // ->where('twilio_credentials_id','=',$request->credential_id)
                                    ->first();

            if ($check_rec && in_array('check_rec', $conditions)) {
                if ($check_rec->store_website_id == $request->store_website_id && $check_rec->twilio_credentials_id != $request->credential_id) {
                    return new JsonResponse(['status' => 0, 'message' => 'Site already assigned to another Twilio Account']);
                }
            }
        } catch (\Exception $e) {
            //do nothing
        }

        try {
            //create new record
            $number_details = TwilioActiveNumber::where('id', $request->twilio_number_id)->first();
            if ($number_details) {
                $number_details->workspace_sid = $request->workspace_sid;
                $number_details->workflow_sid = $request->workflow_sid;
                $number_details->save();
            }

            $storeWebsiteProduct = StoreWebsiteTwilioNumber::updateOrCreate([
                // "store_website_id" => $request->store_website_id,
                'twilio_active_number_id' => $request->twilio_number_id,
                'twilio_credentials_id' => $request->credential_id,
            ], [
                'store_website_id' => $request->store_website_id,
                'twilio_active_number_id' => $request->twilio_number_id,
                'twilio_credentials_id' => $request->credential_id,
                'message_available' => $request->message_available,
                'message_not_available' => $request->message_not_available,
                'end_work_message' => $request->end_work_message,
                'message_busy' => $request->message_busy,
                'category_menu_message' => $request->category_menu_message,
                'sub_category_menu_message' => $request->sub_category_menu_message,
                'speech_response_not_available_message' => $request->speech_response_not_available_message,
            ]);

            // $assign_number = StoreWebsiteTwilioNumber::create([
            //     'store_website_id' => $request->store_website_id,
            //     'twilio_active_number_id' => $request->twilio_number_id,
            //     'message_available' => $request->message_available,
            //     'message_not_available' => $request->message_not_available,
            //     'message_busy' => $request->message_busy
            // ]);

            return new JsonResponse(['status' => 1, 'message' => 'Number assigned to store website successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => 'Something went wrong']);
        }
    }

    public function twilioCallForward(Request $request)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $number_details = TwilioActiveNumber::where('id', $request->twilio_number_id)->first();
        $account_details = TwilioCredential::where('id', $request->twilio_account_id)->where('twiml_app_sid', '!=', null)->first();
        try {
            TwilioCallForwarding::where(['forwarding_on' => $request->agent_id])->firstOrFail();

            return new JsonResponse(['status' => 0, 'message' => 'Agent already assigned for other no.']);
        } catch (\Exception $e) {
        }
        try {
            //get number details
            $agent_details = User::where('id', $request->agent_id)->first();
            TwilioCallForwarding::where(['twilio_number' => $number_details->phone_number])->delete();
            TwilioCallForwarding::create([
                'twilio_number_sid' => $number_details->sid,
                'twilio_number' => $number_details->phone_number,
                'forwarding_on' => $request->agent_id,
                'twilio_active_number_id' => $request->twilio_number_id,
            ]);
            // $base_url = env('APP_URL');
            $base_url = config('env.APP_URL');
            //update webhook url on twilio console using api
            $ch = curl_init();
            $url = "https://api.twilio.com/2010-04-01/Accounts/' . $account_details->account_id . '/IncomingPhoneNumbers/' . $number_details->sid . '.json'";
            curl_setopt($ch, CURLOPT_URL, 'https://api.twilio.com/2010-04-01/Accounts/' . $account_details->account_id . '/IncomingPhoneNumbers/' . $number_details->sid . '.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            //curl_setopt($ch, CURLOPT_POSTFIELDS,"VoiceUrl=http://5be3e7a64b37.ngrok.io/run-webhook/".$number_details->sid."");
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'VoiceUrl=' . $base_url . '/run-webhook/' . $number_details->sid . '');
            curl_setopt($ch, CURLOPT_USERPWD, $account_details->account_id . ':' . $account_details->auth_token);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);     

            LogRequest::log($startTime, $url, 'POST', json_encode(['VoiceUrl=' . $base_url . '/run-webhook/' . $number_details->sid]), json_decode($result), $httpcode, \App\Http\Controllers\TwilioController::class, 'twilioCallForward');

            return new JsonResponse(['status' => 1, 'message' => 'Number forwarded to agent successfully.']);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function runWebhook($sid)
    {
        Log::channel('customerDnd')->info('Webhook called successfully');
        $twiml = new VoiceResponse();
        //get forwarded no. of this twilio_sid
        $forwarding = TwilioCallForwarding::where('twilio_number_sid', '=', $sid)->first();
        Log::channel('customerDnd')->info('forwarding number details ' . $forwarding->forwarding_on);
        Log::channel('customerDnd')->info('number dialled');
        $twiml->Say('Please wait , we are connecting your call');
        $twiml->dial($forwarding->forwarding_on, ['record' => 'record-from-ringing-dual']);
        $twiml->hangup();
        echo $twiml;
        exit;
    }

    public function callManagement(Request $request)
    {
        $twilio_accounts = TwilioCredential::where('status', true)->where('twiml_app_sid', '!=', null)->get();
        $id = $request->get('id');
        if ($id != null) {
            $twilio_account_details = TwilioCredential::where(['id' => $id])->with('numbers.assigned_stores', 'numbers.forwarded.forwarded_number_details.user_availabilities')->first();
            $customer_role_users = RoleUser::where(['role_id' => 50])->with('user')->get();

            return view('twilio.manage-calls', compact('twilio_accounts', 'customer_role_users', 'twilio_account_details'));
        }

        return view('twilio.manage-calls', compact('twilio_accounts'));
    }

    public function getIncomingList(Request $request, $number_sid, $phone_number)
    {
        try {
            $id = $request->get('id');
            $check_account = TwilioCredential::where(['id' => $id])->where('twiml_app_sid', '!=', null)->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Calls.json?To=' . $phone_number;
            $incoming_calls = TwilioHelper::curlGetRequest($url, $sid, $token);
            $incoming_calls = json_decode($incoming_calls);

            return view('twilio.incoming-calls', compact('incoming_calls', 'phone_number'));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function incomingCallRecording(Request $request, $call_sid)
    {
        $id = $request->get('id');
        $check_account = TwilioCredential::where(['id' => $id])->where('twiml_app_sid', '!=', null)->firstOrFail();
        $sid = $check_account->account_id;
        $token = $check_account->auth_token;
        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Calls/' . $call_sid . '/Recordings.json';
        $incoming_calls_recordings = TwilioHelper::curlGetRequest($url, $sid, $token);
        $incoming_calls_recordings = json_decode($incoming_calls_recordings);
        if (count($incoming_calls_recordings->recordings) > 0) {
            $rec_sid = $incoming_calls_recordings->recordings[0]->sid;
        } else {
            return redirect()->back()->with('error', 'Recording not found');
        }
        $file = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Recordings/' . $rec_sid . '.mp3';
        header('Content-type: application/x-file-to-save');
        header('Content-Disposition: attachment; filename=' . basename($file));
        readfile($file);
        exit;
    }

    public function CallRecordings($id)
    {
        try {
            $check_account = TwilioCredential::where(['id' => $id])->where('twiml_app_sid', '!=', null)->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);
            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Recordings.json?__referrer=runtime&Format=json&PageSize=100&Page=0';
            $result = TwilioHelper::curlGetRequest($url, $sid, $token);
            $result = json_decode($result);

            return view('twilio.manage-recordings', compact('result', 'id'));
        } catch (\Exception $e) {
            return redirect('twilio/manage-numbers')->withErrors(['Undefined twilio account']);
        }
    }

    public function downloadRecording(Request $request, $recording_id)
    {
        $id = $request->get('id');
        $check_account = TwilioCredential::where(['id' => $id])->where('twiml_app_sid', '!=', null)->firstOrFail();
        $sid = $check_account->account_id;
        $file = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Recordings/' . $recording_id . '.mp3';
        header('Content-type: application/x-file-to-save');
        header('Content-Disposition: attachment; filename=' . basename($file));
        readfile($file);
        exit;
    }

    public function eventsFromFront(Request $request)
    {
        //dump($request->all());
        // Create Twilio Log
        $inputArray = $request->input();
        $this->createTwilioLog($request, $inputArray, 'Twilio From Event Called here ');
        //TwilioLog::create(['log'=>'Twilio From Event Called here '.json_encode($request->input())]);
        $status = $request->status ?? null;
        $phone = str_replace('+', '', $request->From ?? '+');
        $call_id = $request->CallSid;
        $customer = Customer::where('phone', $phone)->first();
        $call_history = CallHistory::where('call_id', $call_id)->first();
        if (! $call_history) {
            if ($customer) {
                $history = new CallHistory();
                $history->customer_id = $customer->id;
                $history->status = $status;
                $history->call_id = $call_id;
                $history->store_website_id = $this->getStoreWebsiteId($request);
                $history->save();
            }

            return response()->json(true);
        }

        return response()->json(false);
    }

    public function setStorePhoneNumberAndGetWebsite($sid, $phone)
    {
        $twilio = TwilioCredential::where('account_id', $sid)->where('twiml_app_sid', '!=', null)->first();
        if ($twilio) {
            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/IncomingPhoneNumbers.json';
            $result = TwilioHelper::curlPostRequest($url, 'PhoneNumber=' . $phone, $sid . ':' . $twilio->auth_token);
            $result = json_decode($result);
            if ($result->sid) {
                $active_number = new TwilioActiveNumber();
                $active_number->sid = $result->sid ?? null;
                $active_number->account_sid = $result->account_sid ?? null;
                $active_number->friendly_name = $result->friendly_name ?? null;
                $active_number->phone_number = $result->phone_number ?? null;
                $active_number->voice_url = $result->voice_url ?? null;
                $active_number->date_created = $result->date_created ?? null;
                $active_number->date_updated = $result->date_updated ?? null;
                $active_number->sms_url = $result->sms_url ?? null;
                $active_number->voice_receive_mode = $result->voice_receive_mode ?? null;
                $active_number->api_version = $result->api_version ?? null;
                $active_number->voice_application_sid = $result->voice_application_sid ?? null;
                $active_number->sms_application_sid = $result->sms_application_sid ?? null;
                $active_number->trunk_sid = $result->trunk_sid ?? null;
                $active_number->emergency_status = $result->emergency_status ?? null;
                $active_number->emergency_address_sid = $result->emergency_address_sid ?? null;
                $active_number->address_sid = $result->address_sid ?? null;
                $active_number->identity_sid = $result->identity_sid ?? null;
                $active_number->bundle_sid = $result->bundle_sid ?? null;
                $active_number->uri = $result->uri ?? null;
                $active_number->status = $result->status ?? null;
                $active_number->twilio_credential_id = $twilio->id;
                $active_number->save();
                $web_site = StoreWebsiteTwilioNumber::where('twilio_active_number_id', $active_number->id)->first();
                if ($web_site) {
                    return $web_site;
                } else {
                    $answer = $this->create_store_website_twilio_numbers($active_number);
                    if ($answer) {
                        return $answer;
                    }
                }
            }
        }

        return false;
    }

    public function create_store_website_twilio_numbers($active_number)
    {
        $store_web_site = new StoreWebsiteTwilioNumber();
        $web_site = StoreWebsite::first();
        if (! $web_site) {
            return false;
        }
        $store_web_site->store_website_id = $web_site->id;
        $store_web_site->twilio_active_number_id = $active_number->id;
        $store_web_site->save();

        return $store_web_site;
    }

    private function getStoreWebsiteId($request)
    {
        $to = $request->To ?? '';
        $sid = $request->AccountSid ?? '';
        if ($to && $sid) {
            $active_number = TwilioActiveNumber::where('phone_number', $to)->first();
            if ($active_number) {
                $web_site = StoreWebsiteTwilioNumber::where('twilio_active_number_id', $active_number->id)->first();
                if ($web_site) {
                    return $web_site->store_website_id;
                } else {
                    $answer = $this->create_store_website_twilio_numbers($active_number);
                    if ($answer) {
                        return $answer->store_website_id;
                    }
                }
            } else {
                $answer = $this->setStorePhoneNumberAndGetWebsite($sid, $to);
                if ($answer) {
                    return $answer->store_website_id;
                }
            }
        }

        return null;
    }

    public function manageUsers(Request $request)
    {
        $website_id = $request->website_id;
        TwilioAgent::where('status', '1')->where('store_website_id', $website_id)->update(['status' => 0]);

        if ($request->form_data) {
            foreach ($request->form_data as $key => $value) {
                $storeWebsiteProduct = TwilioAgent::updateOrCreate([
                    'user_id' => $value,
                    'store_website_id' => $website_id,
                ], [
                    'user_id' => $value,
                    'store_website_id' => $website_id,
                    'status' => 1,
                ]);
            }
        }

        return new JsonResponse(['status' => 1, 'message' => 'Twilio Agent Added Successfully']);
    }

    public function setWebsiteTime(Request $request)
    {
        $storeWebsiteProduct = TwilioSitewiseTime::updateOrCreate([
            'store_website_id' => $request->site_id,
        ], [
            'store_website_id' => $request->site_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return new JsonResponse(['status' => 1, 'message' => 'Time Set Successfully']);
    }

    public function getWebsiteAgent(Request $request)
    {
        // $twilio_user_list = User::LeftJoin('twilio_agents','user_id','users.id')
        // //->where('twilio_agents.store_website_id',$request->website_id)
        // ->select('users.*','twilio_agents.status',  'twilio_agents.store_website_id')
        // ->orderBy('users.name', 'ASC')->get();

        $twilio_user = User::orderBy('name')->get();
        $store_website_list = [];
        $website_credentials = StoreWebsiteTwilioNumber::where('store_website_id', $request->website_id)->select('twilio_credentials_id')->first();
        if ($website_credentials) {
            $twilio_credentials_id = $website_credentials->twilio_credentials_id;
            $website_credentials = StoreWebsiteTwilioNumber::where('twilio_credentials_id', $twilio_credentials_id)->get()->toArray();
            $store_website_list = array_unique(array_column($website_credentials, 'store_website_id'));
        }

        // $twilio_agent = TwilioAgent::where('store_website_id',$request->website_id)->get();
        $twilio_agent = TwilioAgent::get();

        $twilio_agent_arr = [];
        $twilio_user_list = [];

        foreach ($twilio_agent as $key => $val) {
            if (in_array($val->store_website_id, $store_website_list)) {
                $twilio_agent_arr[$val->user_id]['is_same_website'] = 1;
            }

            $twilio_agent_arr[$val->user_id]['status'] = $val->status;
            $twilio_agent_arr[$val->user_id]['website'] = $val->store_website_id;
        }

        foreach ($twilio_user as $key => $val) {
            if (array_key_exists($val->id, $twilio_agent_arr)) {
                $twilio_user_list[$key] = $val;
                $twilio_user_list[$key]['status'] = $twilio_agent_arr[$val->id]['status'];
                $twilio_user_list[$key]['website'] = $twilio_agent_arr[$val->id]['website'];
                $twilio_user_list[$key]['is_same_website'] = ($twilio_agent_arr[$val->id]['is_same_website'] ?? 0);
            } else {
                $twilio_user_list[$key] = $val;
            }
        }

        // $twilio_user_list = User::LeftJoin('twilio_agents','user_id','users.id')->select('users.*','twilio_agents.status')->orderBy('users.name', 'ASC')->get();

        // $twilio_user_list = User::LeftJoin('twilio_agents','user_id','users.id')
        // //->where('twilio_agents.store_website_id',$request->website_id)
        // ->select('users.*','twilio_agents.status', DB::raw('IF(twilio_agents.store_website_id = '. $request->website_id .', "YES", "NO") as website'))
        // ->orderBy('users.name', 'ASC')->get();

        return new JsonResponse(['status' => 1, 'twilio_user_list' => $twilio_user_list]);
    }

    public function setTwilioKey(Request $request)
    {
        $check_this_action = TwilioKeyOption::where('website_store_id', $request->get('website_store_id'))
        ->where('description', $request->get('option'))
        ->first();
        $conditionsWithIds = TwilioCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $conditions = array_keys($conditionsWithIds);

        if ($check_this_action && $request->get('up_id') == 0 && in_array('check_this_action', $conditions)) {
            return new JsonResponse(['status' => 0, 'message' => 'This Option Alreday Set in Another Key']);
        } else {
            $call_history = TwilioKeyOption::updateOrCreate([
                'key' => $request->get('key_no'),
                'website_store_id' => $request->get('website_store_id'),
            ], [
                'key' => $request->get('key_no'),
                'description' => $request->get('option'),
                'details' => $request->get('description'),
                'message' => $request->get('message'),
                'website_store_id' => $request->get('website_store_id'),
            ]);

            return new JsonResponse(['status' => 1, 'message' => 'Option Set Successfully']);
        }
    }

    public function saveTwilioGreetingMessage(Request $request)
    {
        StoreWebsite::where('id', $request->get('website_store_id'))->update(['twilio_greeting_message' => $request->get('welcome_message')]);

        return new JsonResponse(['status' => 1, 'message' => 'Greeting message Set Successfully']);
    }

    public function getTwilioKeyData(Request $request)
    {
        $keydata = TwilioKeyOption::where('website_store_id', $request->website_store_id)->get();
        $web_id = $request->website_store_id;

        $twilio_key_arr = [];
        $html = '';
        if ($keydata) {
            foreach ($keydata as $key => $value) {
                $twilio_key_arr[$value->key]['option'] = $value->description;
                $twilio_key_arr[$value->key]['desc'] = $value->details;
                $twilio_key_arr[$value->key]['message'] = $value->message;
                $twilio_key_arr[$value->key]['id'] = $value->id;
            }

            $html = view('twilio.twilio_key_data', compact('twilio_key_arr', 'web_id'))->render(); //dd( $html);
        }
        $greetingMessage = StoreWebsite::where('id', $request->website_store_id)->pluck('twilio_greeting_message')->first();

        return ['html' => $html, 'welcome_message' => $greetingMessage];
    }

    public function getTwilioKeyDataOptions(Request $request)
    {
        $store_websites = StoreWebsite::LeftJoin('twilio_sitewise_times as tst', 'tst.store_website_id', 'store_websites.id')->select('store_websites.*', 'tst.start_time', 'tst.end_time')->orderBy('store_websites.website', 'ASC');
        if ($request->web_site_id) {
            $store_websites = $store_websites->where('store_websites.id', $request->web_site_id);
        }
        if ($request->website_ids) {
            $store_websites = $store_websites->whereIn('store_websites.id', $request->website_ids);
        }
        $store_websites = $store_websites->get();
        $web_id = $request->website_store_id;
        $web_site_id = $request->web_site_id;

        $twilio_key_arr = [];
        $html = '';
      
        //if($keydata)

        //{
        foreach ($store_websites as $store_website_data) {
            $keydata = TwilioKeyOption::where('website_store_id', $store_website_data->id)->get();

            foreach ($keydata as $key => $value) {
                $twilio_key_arr[$store_website_data->id][$value->key]['option'] = $value->description;
                $twilio_key_arr[$store_website_data->id][$value->key]['desc'] = $value->details;
                $twilio_key_arr[$store_website_data->id][$value->key]['message'] = $value->message;
                $twilio_key_arr[$store_website_data->id][$value->key]['id'] = $value->id;
            }
        }

        return view('twilio.twilio_key_data_options', compact('twilio_key_arr', 'web_id', 'store_websites', 'web_site_id'))->render(); //dd( $html);
        //}
        //$greetingMessage = StoreWebsite::where('id', $request->website_store_id)->pluck('twilio_greeting_message')->first();
        //return ['html'=>$html, 'welcome_message'=>$greetingMessage];
    }

    public function setTwilioWorkSpace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workspace_name' => 'required',
            'callback_url' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0] . '<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }

        try {
            $account_id = $request->account_id;
            $check_account = TwilioCredential::where(['id' => $account_id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);
            $workspace_name = $request->workspace_name;
            $workspace = $twilio->taskrouter->v1->workspaces
            ->create($workspace_name, // friendlyName
                [
                    'eventCallbackUrl' => $request->callback_url,
                    'template' => 'FIFO',
                ]
            );
            TwilioWorkspace::create([
                'twilio_credential_id' => $account_id,
                'workspace_name' => $workspace_name,
                'workspace_sid' => $workspace->sid,
                'callback_url' => $request->callback_url,
            ]);

            return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Workspace Created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => 'Something went wrong']);
        }
    }

    public function deleteTwilioWorkSpace(Request $request)
    {
        $workspace_id = $request->workspace_id;

        $getdata = TwilioWorkspace::where('id', $workspace_id)->first();

        $check_account = TwilioCredential::where(['id' => $getdata->twilio_credential_id])->firstOrFail();
        $sid = $check_account->account_id;
        $token = $check_account->auth_token;
        $twilio = new Client($sid, $token);

        $twilio->taskrouter->v1->workspaces($getdata->workspace_sid)->delete();

        TwilioWorkspace::where('id', $workspace_id)->update(['deleted' => 1]);

        return new JsonResponse(['code' => 200, 'message' => 'Workspace deleted successfully']);
    }

    public function createTwilioPriority(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'priority_no' => 'required',
            'priority_name' => 'required',
            'account_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0] . '<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }

        $accountId = $request->account_id;
        $priorityNo = $request->priority_no;
        $priorityName = $request->priority_name;

        $check_name = TwilioPriority::where('priority_no', $priorityNo)->where('account_id', $accountId)->where('twilio_priorities.deleted_at', null)->first();

        if ($check_name) {
            return new JsonResponse(['status' => 'failed', 'statusCode' => 500, 'message' => 'This Priority Number already exists']);
        } else {
            $priority_latest = TwilioPriority::create([
                'priority_no' => $priorityNo,
                'account_id' => $accountId,
                'priority_name' => $priorityName,
            ]);
            $priority_latest_record = TwilioPriority::where('twilio_priorities.account_id', $accountId)
            ->where('twilio_priorities.deleted_at', null)
            ->where('twilio_priorities.id', $priority_latest->id)
            ->select('twilio_priorities.*')
            ->first();

            return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Priority Created successfully', 'data' => $priority_latest_record]);
        }
    }

    public function deleteTwilioPriority(Request $request)
    {
        $priority_id = $request->priority_id;

        $getdata = TwilioPriority::where('id', $priority_id)->first();

        TwilioPriority::where('id', $priority_id)->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return new JsonResponse(['code' => 200, 'message' => 'Priority deleted successfully']);
    }

    public function createTwilioWorker(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workspace_id' => 'required',
            // 'worker_name' => 'required',
            'user_id' => 'required|not_in:0',
            'worker_phone' => 'required',
            'worker_priority' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0] . '<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }

        $workspace_id = $request->workspace_id;
        $twilio_credential_id = $request->account_id;
        $user_id = $request->user_id;

        $user = User::find($user_id);

        $check_name = TwilioWorker::where('user_id', $user_id)->where('twilio_workspace_id', $workspace_id)->where('deleted', 0)->first();

        if ($check_name) {
            return new JsonResponse(['status' => 'failed', 'statusCode' => 500, 'message' => 'This Worker already exists']);
        } else {
            $workspace_data = TwilioWorkspace::where('id', $workspace_id)->first();

            $check_account = TwilioCredential::where(['id' => $workspace_data->twilio_credential_id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);
            $activity = clone $twilio;

            $activtiySid = '';
            $activities = $activity->taskrouter->v1->workspaces($workspace_data->workspace_sid)
            ->activities
            ->read(['friendlyName' => 'Offline'], 1);
            foreach ($activities as $record) {
                $activtiySid = $record->sid;
            }

            $worker = $twilio->taskrouter->v1->workspaces($workspace_data->workspace_sid)->workers->create($user->name,
                ['attributes' => json_encode([
                    'customer_value' => $request->worker_priority,
                    'phone' => $request->worker_phone,
                    'contact_uri' => 'client:customer_call_agent_' . $user->id,
                ]),
                    'activitySid' => $activtiySid,
                ]
            );

            TwilioWorker::create([
                'twilio_credential_id' => $twilio_credential_id,
                'twilio_workspace_id' => $workspace_id,
                'worker_name' => $user->name,
                'user_id' => $user->id,
                'priority' => json_encode($request->worker_priority),
                'worker_sid' => $worker->sid,
                'worker_phone' => $request->worker_phone,
            ]);

            $worker_latest_record = TwilioWorker::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_workers.twilio_workspace_id')
            ->where('twilio_workers.worker_name', $user->name)
            ->where('twilio_workers.twilio_workspace_id', $workspace_id)
            ->where('twilio_workers.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_workers.*')
            ->first();

            return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Worker Created successfully', 'data' => $worker_latest_record]);
        }
    }

    public function deleteTwilioWorker(Request $request)
    {
        $worker_id = $request->worker_id;

        $getdata = TwilioWorker::where('id', $worker_id)->first();
        $get_workspace_data = TwilioWorkspace::where('id', $getdata->twilio_workspace_id)->first();

        $check_account = TwilioCredential::where(['id' => $getdata->twilio_credential_id])->firstOrFail();
        $sid = $check_account->account_id;
        $token = $check_account->auth_token;
        $twilio = new Client($sid, $token);

        $twilio->taskrouter->v1->workspaces($get_workspace_data->workspace_sid)->workers($getdata->worker_sid)->delete();

        TwilioWorker::where('id', $worker_id)->update(['deleted' => 1]);

        return new JsonResponse(['code' => 200, 'message' => 'Worker deleted successfully']);
    }

    public function createTwilioWorkflow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workspace_id' => 'required',
            'workflow_name' => 'required',
            'fallback_assignment_callback_url' => 'required',
            'assignment_callback_url' => 'required',
            'task_queue' => 'required',
            'worker_reservation_timeout' => 'required',
            'task_timeout' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0] . '<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }

        $workspace_id = $request->workspace_id;
        $workflow_name = $request->workflow_name;
        $twilio_credential_id = $request->account_id;
        $fallback_assignment_callback_url = $request->fallback_assignment_callback_url;
        $assignment_callback_url = $request->assignment_callback_url;
        $task_queue_id = $request->task_queue;

        $check_name = TwilioWorkflow::where('workflow_name', $workflow_name)->where('twilio_workspace_id', $workspace_id)->first();
        $task_queue_sid = TwilioTaskQueue::where('id', $task_queue_id)->where('twilio_workspace_id', $workspace_id)->pluck('task_queue_sid')->first();

        if ($check_name) {
            return new JsonResponse(['status' => 'failed', 'statusCode' => 500, 'message' => 'This workflow already exists']);
        } else {
            $workspace_data = TwilioWorkspace::where('id', $workspace_id)->first();

            $check_account = TwilioCredential::where(['id' => $workspace_data->twilio_credential_id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);

            $workflow = $twilio->taskrouter->v1->workspaces($workspace_data->workspace_sid)->workflows->create($workflow_name,
                json_encode([
                    'task_routing' => [
                        'filters' => [
                            [
                                'expression' => '1==1',
                                'targets' => [
                                    [
                                        'queue' => $task_queue_sid,
                                        'expression' => 'task.type IN worker.customer_value',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]), [
                    'assignmentCallbackUrl' => $request->assignment_callback_url,
                    'fallbackAssignmentCallbackUrl' => $request->fallback_assignment_callback_url,
                    'taskReservationTimeout' => $request->worker_reservation_timeout,
                ]);

            TwilioWorkflow::create([
                'twilio_credential_id' => $twilio_credential_id,
                'twilio_workspace_id' => $workspace_id,
                'workflow_name' => $workflow_name,
                'workflow_sid' => $workflow->sid,
                'task_queue_id' => $task_queue_id,
                'fallback_assignment_callback_url' => $request->fallback_assignment_callback_url,
                'assignment_callback_url' => $request->assignment_callback_url,
                'worker_reservation_timeout' => $request->worker_reservation_timeout,
                'task_timeout' => $request->task_timeout,
            ]);

            $workflow_latest_record = TwilioWorkflow::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_workflows.twilio_workspace_id')
            ->where('twilio_workflows.workflow_name', $workflow_name)
            ->where('twilio_workflows.twilio_workspace_id', $workspace_id)
            ->where('twilio_workflows.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_workflows.*')
            ->first();

            return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Workflow Created successfully', 'data' => $workflow_latest_record, 'type' => 'workflowList']);
        }
    }

    public function deleteTwilioWorkflow(Request $request)
    {
        $workflow_id = $request->id;
        $getdata = TwilioWorkflow::where('id', $workflow_id)->first();
        if ($getdata != null) {
            $get_workspace_data = TwilioWorkspace::where('id', $getdata->twilio_workspace_id)->first();
            $check_account = TwilioCredential::where(['id' => $getdata->twilio_credential_id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);

            $twilio->taskrouter->v1->workspaces($get_workspace_data->workspace_sid)->workflows($getdata->workflow_sid)->delete();

            TwilioWorkflow::where('id', $workflow_id)->update(['deleted' => 1]);

            return new JsonResponse(['code' => 200, 'message' => 'Workflow deleted successfully']);
        } else {
            return new JsonResponse(['code' => 500, 'message' => 'Workflow not found']);
        }
    }

    public function editTwilioWorkflow(Request $request)
    {
        $workflow_id = $request->id;
        $getdata = TwilioWorkflow::where('id', $workflow_id)->first();
        if ($getdata != null) {
            $get_workspace_data = TwilioWorkspace::where('id', $getdata->twilio_workspace_id)->first();
            $check_account = TwilioCredential::where(['id' => $getdata->twilio_credential_id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);

            $twilio->taskrouter->v1->workspaces($get_workspace_data->workspace_sid)->workflows($getdata->workflow_sid)->update([
                'taskReservationTimeout' => $request->workerReservationTimeout,
            ]);

            TwilioWorkflow::where('id', $workflow_id)->update([
                'worker_reservation_timeout' => $request->workerReservationTimeout,
                'task_timeout' => $request->taskTimeout,
            ]);

            return new JsonResponse(['code' => 200, 'message' => 'Workflow update successfully']);
        } else {
            return new JsonResponse(['code' => 500, 'message' => 'Workflow not found']);
        }
    }

    public function createTwilioActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workspace_id' => 'required',
            'activity_name' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0] . '<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }

        $workspace_id = $request->workspace_id;
        $activity_name = $request->activity_name;
        $availability = $request->availability;
        $twilio_credential_id = $request->account_id;

        $check_name = TwilioActivity::where('activity_name', $activity_name)->where('twilio_workspace_id', $workspace_id)->first();

        if ($check_name) {
            return new JsonResponse(['status' => 'failed', 'statusCode' => 500, 'message' => 'This Activity already exists']);
        } else {
            $workspace_data = TwilioWorkspace::where('id', $workspace_id)->first();

            $check_account = TwilioCredential::where(['id' => $workspace_data->twilio_credential_id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);

            $twilioAvailability = $twilio->taskrouter->v1->workspaces($workspace_data->workspace_sid)->activities->create($activity_name, [
                'availability' => $availability,
            ]);
            TwilioActivity::create([
                'twilio_credential_id' => $twilio_credential_id,
                'twilio_workspace_id' => $workspace_id,
                'activity_name' => $activity_name,
                'availability' => $availability,
                'activity_sid' => $twilioAvailability->sid,
            ]);

            $activities_latest_record = TwilioActivity::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_activities.twilio_workspace_id')
            ->where('twilio_activities.activity_name', $activity_name)
            ->where('twilio_activities.twilio_workspace_id', $workspace_id)
            ->where('twilio_activities.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_activities.*')
            ->first();
            if (isset($activities_latest_record['availability'])) {
                if ($activities_latest_record['availability'] == 1) {
                    $activities_latest_record['availability'] = 'True';
                } else {
                    $activities_latest_record['availability'] = 'False';
                }
            }

            return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Activity Created successfully', 'data' => $activities_latest_record, 'type' => 'activityList']);
        }
    }

    public function deleteTwilioActivity(Request $request)
    {
        $activity_id = $request->id;
        $getdata = TwilioActivity::where('id', $activity_id)->first();
        if ($getdata != null) {
            $get_workspace_data = TwilioWorkspace::where('id', $getdata->twilio_workspace_id)->first();
            $check_account = TwilioCredential::where(['id' => $getdata->twilio_credential_id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);

            $twilio->taskrouter->v1->workspaces($get_workspace_data->workspace_sid)->activities($getdata->activity_sid)->delete();

            TwilioActivity::where('id', $activity_id)->update(['deleted' => 1]);

            return new JsonResponse(['code' => 200, 'message' => 'Activity deleted successfully']);
        } else {
            return new JsonResponse(['code' => 500, 'message' => 'Activity not found']);
        }
    }

    public function createTwilioTaskQueue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workspace_id' => 'required',
            'task_queue_name' => 'required',
            'assignment_activity_id' => 'required',
            'reservation_activity_id' => 'required',
            'task_order' => 'required',
            'max_reserved_workers' => 'required',
            'queue_expression' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0] . '<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }

        $workspace_id = $request->workspace_id;
        $task_queue_name = $request->task_queue_name;
        $assignmentActivitySid = $reservationActivitySid = null;
        if ($request->assignment_activity_id) {
            $assignmentActivitySid = TwilioActivity::where('id', $assignmentActivitySid)->where('twilio_workspace_id', $workspace_id)->pluck('activity_sid')->first();
        }
        if ($request->reservation_activity_id) {
            $reservationActivitySid = TwilioActivity::where('id', $request->reservation_activity_id)->where('twilio_workspace_id', $workspace_id)->pluck('activity_sid')->first();
        }
        $twilio_credential_id = $request->account_id;

        $check_name = TwilioTaskQueue::where('task_queue_name', $task_queue_name)->where('twilio_workspace_id', $workspace_id)->first();

        if ($check_name) {
            return new JsonResponse(['status' => 'failed', 'statusCode' => 500, 'message' => 'This Task Queue already exists']);
        } else {
            $workspace_data = TwilioWorkspace::where('id', $workspace_id)->first();

            $check_account = TwilioCredential::where(['id' => $workspace_data->twilio_credential_id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);

            $twilioTaskQueue = $twilio->taskrouter->v1->workspaces($workspace_data->workspace_sid)->taskQueues->create($task_queue_name, [
                'assignmentActivitySid' => $assignmentActivitySid,
                'reservationActivitySid' => $reservationActivitySid,
                'targetWorkers' => $request->queue_expression,
                'maxReservedWorkers' => $request->max_reserved_workers,
                'taskOrder' => $request->task_order,
            ]);
            TwilioTaskQueue::create([
                'twilio_credential_id' => $twilio_credential_id,
                'twilio_workspace_id' => $workspace_id,
                'task_queue_name' => $task_queue_name,
                'task_order' => $request->task_order,
                'reservation_activity_id' => $request->reservation_activity_id,
                'assignment_activity_id' => $request->assignment_activity_id,
                'target_workers' => $request->queue_expression,
                'max_reserved_workers' => $request->max_reserved_workers,
                'task_queue_sid' => $twilioTaskQueue->sid,
            ]);

            $task_latest_record = TwilioTaskQueue::join('twilio_workspaces', 'twilio_workspaces.id', 'twilio_task_queue.twilio_workspace_id')
            ->where('twilio_task_queue.task_queue_name', $task_queue_name)
            ->where('twilio_task_queue.twilio_workspace_id', $workspace_id)
            ->where('twilio_task_queue.deleted', 0)
            ->select('twilio_workspaces.workspace_name', 'twilio_task_queue.*')
            ->first();

            return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Task Queue Created successfully', 'data' => $task_latest_record, 'type' => 'taskQueueList']);
        }
    }

    public function deleteTwilioTaskQueue(Request $request)
    {
        $taskQueueId = $request->id;
        $getdata = TwilioTaskQueue::where('id', $taskQueueId)->first();
        if ($getdata != null) {
            $get_workspace_data = TwilioWorkspace::where('id', $getdata->twilio_workspace_id)->first();
            $check_account = TwilioCredential::where(['id' => $getdata->twilio_credential_id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);

            $twilio->taskrouter->v1->workspaces($get_workspace_data->workspace_sid)->taskQueues($getdata->task_queue_sid)->delete();

            TwilioTaskQueue::where('id', $taskQueueId)->update(['deleted' => 1]);

            return new JsonResponse(['code' => 200, 'message' => 'TwilioTaskQueue deleted successfully']);
        } else {
            return new JsonResponse(['code' => 500, 'message' => 'TwilioTaskQueue not found']);
        }
    }

    public function fetchActivitiesFromWorkspace($workspaceId)
    {
        $twilioActivities[0] = TwilioActivity::where('twilio_workspace_id', $workspaceId)->where('deleted', 0)->where('availability', 0)->pluck('activity_name', 'id')->toArray();
        $twilioActivities[1] = $twilioActivities[0];

        return $twilioActivities;
    }

    public function fetchTaskQueueFromWorkspace($workspaceId)
    {
        $twilioTaskQueue = TwilioTaskQueue::where('twilio_workspace_id', $workspaceId)->pluck('task_queue_name', 'id')->where('deleted', 0)->toArray();

        return $twilioTaskQueue;
    }

    public function twilioErpLogs(Request $request)
    {
        $input = $request->input();
        $twilioLogs = TwilioLog::orderBy('id', 'desc');
        if (isset($input['caller'])) {
            $twilioLogs = $twilioLogs->where('phone', 'like', '%' . $input['caller'] . '%');
        }
        if (isset($input['log'])) {
            $twilioLogs = $twilioLogs->where('log', 'like', '%' . $input['log'] . '%');
        }
        if ($request->date) {
            $dataArr = $twilioLogs->where('created_at', 'LIKE', '%' . $request->date . '%');
        }
        $twilioLogs = $twilioLogs->paginate(20);

        $recordedText = 'International delivery';

        return view('twilio.erp_logs', compact('twilioLogs', 'input'));
    }

    public function speechToTextLogs(Request $request)
    {
        $input = $request->input();
        $twilioLogs = TwilioLog::where('type', 'speech')->orderBy('id', 'desc');
        if (isset($input['caller'])) {
            $twilioLogs = $twilioLogs->where('phone', 'like', '%' . $input['caller'] . '%');
        }
        if (isset($input['log'])) {
            $twilioLogs = $twilioLogs->where('log', 'like', '%' . $input['log'] . '%');
        }
        $twilioLogs = $twilioLogs->paginate(20);

        return view('twilio.speech_to_text_logs', compact('twilioLogs', 'input'));
    }

    /**
     * This function is use for list of Twilio call block
     *
     * @return view
     */
    public function callBlocks(Request $request)
    {
        $title = 'Twilio Call Blocks';
        try {
            $input = $request->input();
            $twilioCallBlocks = TwilioCallBlock::select('twilio_call_blocks.customer_id', 'twilio_call_blocks.id', 'twilio_call_blocks.twilio_credentials_id', 'twilio_call_blocks.customer_website_id', 'twilio_call_blocks.twilio_number_website_id', 'twilio_call_blocks.customer_number', 'twilio_call_blocks.twilio_number', 'twilio_call_blocks.created_at', 'c.name AS customerName', 'tc.twilio_email', 'sw.website AS customerWebsite', 'swtc.website AS twWebsite')
            ->leftjoin('customers AS c', 'c.id', 'twilio_call_blocks.customer_id')
            ->leftjoin('twilio_credentials AS tc', 'tc.id', 'twilio_call_blocks.twilio_credentials_id')
            ->leftjoin('store_websites AS sw', 'sw.id', 'twilio_call_blocks.customer_website_id')
            ->leftjoin('store_websites AS swtc', 'swtc.id', 'twilio_call_blocks.twilio_number_website_id')
            ->orderBy('twilio_call_blocks.id', 'desc');

            if (isset($input['search_twilio_number'])) {
                $twilioCallBlocks = $twilioCallBlocks->where('twilio_call_blocks.twilio_number', 'like', '%' . $input['search_twilio_number'] . '%');
            }
            if (isset($input['search_customer_number'])) {
                $twilioCallBlocks = $twilioCallBlocks->where('twilio_call_blocks.customer_number', 'like', '%' . $input['search_customer_number'] . '%');
            }
            $twilioCallBlocks = $twilioCallBlocks->paginate(20);
            //$twilioCallBlocks = $twilioCallBlocks->get();
            //dd($twilioCallBlocks);
            return view('twilio.twilio-call-block   ', compact('twilioCallBlocks', 'input'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'please try again');
        }
    }

    /**
     * This funcrtion is use for delete Call block
     *
     * @return JsonResponse
     */
    public function deleteCallBlocks(Request $request, $ids = '')
    {
        try {
            $idArr = explode(',', $request->ids);
            $callBlock = TwilioCallBlock::whereIn('id', $idArr)->delete();
            if ($callBlock != 0) {
                return response()->json(['code' => 200, 'message' => 'Successfully Deleted']);
            }

            return response()->json(['code' => 500, 'message' => 'Please select any record']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * This function is use for list of Twilio call Statistic
     *
     * @return view
     */
    public function callStatistic(Request $request)
    {
        $title = 'Twilio Call Statistic';
        try {
            $input = $request->input();
            $twilioCallStatistic = TwilioCallStatistic::select('twilio_call_statistics.id', 'twilio_call_statistics.account_sid', 'twilio_call_statistics.call_sid', 'twilio_call_statistics.customer_website_id', 'twilio_call_statistics.twilio_number_website_id', 'twilio_call_statistics.customer_number', 'twilio_call_statistics.twilio_number', 'twilio_call_statistics.created_at', 'c.name AS customerName', 'tc.twilio_email', 'sw.website AS customerWebsite', 'swtc.website AS twWebsite')
            ->leftjoin('customers AS c', 'c.id', 'twilio_call_statistics.customer_id')
            ->leftjoin('twilio_credentials AS tc', 'tc.id', 'twilio_call_statistics.twilio_credentials_id')
            ->leftjoin('store_websites AS sw', 'sw.id', 'twilio_call_statistics.customer_website_id')
            ->leftjoin('store_websites AS swtc', 'swtc.id', 'twilio_call_statistics.twilio_number_website_id')
            ->orderBy('twilio_call_statistics.id', 'desc');

            if (isset($input['search_account_sid'])) {
                $twilioCallStatistic = $twilioCallStatistic->where('twilio_call_statistics.account_sid', 'like', '%' . $input['search_account_sid'] . '%');
            }
            if (isset($input['search_twilio_number'])) {
                $twilioCallStatistic = $twilioCallStatistic->where('twilio_call_statistics.twilio_number', 'like', '%' . $input['search_twilio_number'] . '%');
            }
            if (isset($input['search_customer_number'])) {
                $twilioCallStatistic = $twilioCallStatistic->where('twilio_call_statistics.customer_number', 'like', '%' . $input['search_customer_number'] . '%');
            }
            $twilioCallStatistic = $twilioCallStatistic->paginate(20);
            //$twilioCallBlocks = $twilioCallBlocks->get();
            //dd($twilioCallBlocks);
            return view('twilio.twilio-call-statistic', compact('twilioCallStatistic', 'input'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'please try again');
        }
    }

    /**
     * This funcrtion is use for delete Call block
     *
     * @return JsonResponse
     */
    public function deleteCallStatistic(Request $request)
    {
        try {
            $idArr = explode(',', $request->ids);
            $callStatistic = TwilioCallStatistic::whereIn('id', $idArr)->delete();
            if ($callStatistic != 0) {
                return response()->json(['code' => 200, 'message' => 'Successfully Deleted']);
            }

            return response()->json(['code' => 500, 'message' => 'Please select any record']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function twilioAccountLogs(Request $request)
    {
        $accountLogs = new TwilioAccountLog();

        if ($request->twiliAccount_email) {
            $accountLogs = $accountLogs->WhereIn('email', $request->twiliAccount_email);
        }
        if ($request->sid) {
            $accountLogs = $accountLogs->where('sid', 'LIKE', '%' . $request->sid . '%');
        }
        if ($request->log) {
            $accountLogs = $accountLogs->where('log', 'LIKE', '%' . $request->log . '%');
        }
        if ($request->date) {
            $accountLogs = $accountLogs->where('created_at', 'LIKE', '%' . $request->date . '%');
        }
        
        $accountLogs = $accountLogs->latest()->paginate(\App\Setting::get('pagination',50));

        $twiliAccountemails = TwilioAccountLog::distinct('email')->pluck('email');


        return view('twilio.account_logs', compact('accountLogs','twiliAccountemails'));
    }

    public function getConditions(Request $request)
    {
        $drConditions = TwilioCondition::all();
        if (($request->condition && $request->condition != null) && ($request->description && $request->description != null)) {
            $conditions = TwilioCondition::where('condition', $request->condition)->where('description', 'LIKE', '%' . $request->description . '%')->get();
        } elseif ($request->description && $request->description != null) {
            $conditions = TwilioCondition::where('description', 'LIKE', '%' . $request->description . '%')->get();
        } elseif ($request->condition && $request->condition != null) {
            $conditions = TwilioCondition::where('condition', $request->condition)->get();
        } else if ($request->active_status != null) {
            $conditions = TwilioCondition::where('status', $request->active_status)->get();
        } else {
            $conditions = TwilioCondition::all();
        }

        return view('twilio.conditions', compact('conditions', 'drConditions'));
    }

    public function updateConditionStatus(Request $request)
    {
        $input = $request->input();
        $twiliConditions  = TwilioCondition::where('id', $input['id'])->update(['status' => $input['status']]);
        $twiliConditions = TwilioCondition::find($input['id']);

        if($twiliConditions->status == 0){
            $statusColor=  TwilioConditionStatus::find(2);
        } else {
            $statusColor=  TwilioConditionStatus::find($twiliConditions->status);
        }

        return response()->json([
            'status' => true,
            'color' => $statusColor->color,
            'message' => 'Status Updated',
            'status_name' => 'success',
        ], 200);

    }

    public function saveMessageTone(Request $request)
    {
        $rules = [
            'end_work_ring' => 'mimes:mp3',
            'intro_ring' => 'mimes:mp3',
            'busy_ring' => 'mimes:mp3',
            'wait_url_ring' => 'mimes:mp3',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'errors' => $validator->errors(), 'statusCode' => 400]);
        }
        $input = $request->input();
        $files = $request->files;
        $params['store_website_id'] = $input['store_website_id'];

        foreach ($files as $key => $file) {
            $fileName = $file->getClientOriginalName();
            $fileNameArray[] = $fileName;

            $params[$key] = $params['store_website_id'] . $key . $fileName;
            $file->move(public_path('twilio'), $params[$key]);
        }
        TwilioMessageTone::updateOrCreate(['store_website_id' => $params['store_website_id']], $params);

        return response()->json(['status' => 'success', 'message' => 'Message tones updated', 'statusCode' => 200]);
    }

    public function viewMessageTones(Request $request)
    {
        $twilioMessageTones = StoreWebsite::leftJoin('twilio_message_tones', 'twilio_message_tones.store_website_id', 'store_websites.id')
        ->select('twilio_message_tones.*', 'store_websites.title as website', 'store_websites.id as websiteId')->get();

        $websiteIds = $request->input('website_ids', []);

        if( $websiteIds)
        {
            $twilioMessageTones = StoreWebsite::leftJoin('twilio_message_tones', 'twilio_message_tones.store_website_id', 'store_websites.id')
            ->select('twilio_message_tones.*', 'store_websites.title as website', 'store_websites.id as websiteId')
            ->whereIn('store_websites.id', $websiteIds)
            ->get();
        }
  
        return view('twilio.manage-tones', compact('twilioMessageTones','websiteIds'));
    }

    public function twilioCallJourney(Request $request)
    {
        $call_Journeies = TwilioCallJourney::query();

        // if ($request->id) {
        //     $call_Journeies = $call_Journeies->where('id', $request->id);
        // }

        if ($request->phone) {
            // phone number with search query
            $call_Journeies = $call_Journeies->where('phone', 'like', $request->phone . '%');
        }

        if ($request->store_id) {
            // relation with search query
            $call_Journeies = $call_Journeies->whereHas('twilio_credential', function ($query) use ($request) {
                $query->where('id', 'like', $request->store_id);
            });
        }
        
        if ($request->account_id) {
            $call_Journeies = $call_Journeies->where('account_sid', 'like', $request->account_id . '%');
        }

        if ($request->call_id) {
            $call_Journeies = $call_Journeies->where('call_sid', 'like', $request->call_id . '%');
        }

        if ($request->filled('call_entered') && $request->call_entered === 'yes' || $request->call_entered === 'Yes') {
            $call_Journeies = $call_Journeies->where('call_entered', 1);
        }else if($request->filled('call_entered') && $request->call_entered === 'No' || $request->call_entered === 'no'){
            $call_Journeies = $call_Journeies->where('call_entered','!=', 1);
        }

        if ($request->filled('handled_by_chatbot') && $request->handled_by_chatbot === 'yes' || $request->handled_by_chatbot === 'Yes') {
            $call_Journeies = $call_Journeies->where('handled_by_chatbot', 1);
        }else if($request->filled('handled_by_chatbot') && $request->handled_by_chatbot === 'No' || $request->handled_by_chatbot === 'no'){
            $call_Journeies = $call_Journeies->where('handled_by_chatbot','!=', 1);
        }
        
        if ($request->filled('called_working_hours') && $request->called_working_hours === 'yes' || $request->called_working_hours === 'Yes') {
            $call_Journeies = $call_Journeies->where('called_in_working_hours', 1);
        }else if($request->filled('called_working_hours') && $request->called_working_hours === 'No' || $request->called_working_hours === 'no'){
            $call_Journeies = $call_Journeies->where('called_in_working_hours','!=', 1);
        }

        if ($request->filled('avaiable_agent') && $request->avaiable_agent === 'yes' || $request->avaiable_agent === 'Yes') {
            $call_Journeies = $call_Journeies->where('agent_available', 1);
        }else if($request->filled('avaiable_agent') && $request->avaiable_agent === 'No' || $request->avaiable_agent === 'no'){
            $call_Journeies = $call_Journeies->where('agent_available','!=', 1);
        }

        if ($request->filled('agent_online') && $request->agent_online === 'yes' || $request->agent_online === 'Yes') {
            $call_Journeies = $call_Journeies->where('agent_online', 1);
        }else if($request->filled('agent_online') && $request->agent_online === 'No' || $request->agent_online === 'no'){
            $call_Journeies = $call_Journeies->where('agent_online','!=', 1);
        }

        if ($request->filled('call_answered') && $request->call_answered === 'yes' || $request->call_answered === 'Yes') {
            $call_Journeies = $call_Journeies->where('call_answered', 1);
        }else if($request->filled('call_answered') && $request->call_answered === 'No' || $request->call_answered === 'no'){
            $call_Journeies = $call_Journeies->where('call_answered','!=', 1);
        }

        $call_Journeies = $call_Journeies->with(['twilio_credential:id,account_id,twilio_email'])
                ->orderBy('id', 'desc')
                ->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            $count = $call_Journeies->count();

            $html = view('twilio.partials.call_journey_data', compact('call_Journeies'))->render();

            if (count($call_Journeies)) {
                return ['status' => 1, 'html' => $html, 'count' => $count, 'call_Journeies' => $call_Journeies];
            } else {
                return ['status' => 0, 'html' => '<tr id="noresult_tr"><td colspan="7">No More Records</td></tr>'];
            }
        }

        return view('twilio.call_journey', compact('call_Journeies'));
    }

    /**
     * This function is use for list of Twilio SMS delivery logs
     *
     * @return view
     */
    public function twilioDeliveryLogs(Request $request)
    {

        $query = TwilioMessageDeliveryLogs::orderBy('created_at', 'desc')->with('customers:id,name,email');

        $twiliCoustomerEmails = Customer::select('id', 'email')->get();
    
        if ($request->twilicustomer_email) {
            $query->whereIn('customer_id', $request->twilicustomer_email);
        }
        if ($request->message_id) {
            $query->where('message_sid', 'LIKE', '%' . $request->message_id . '%');
        }
        if ($request->user_from) {
            $query->where('from', 'LIKE', '%' . $request->user_from . '%');
        }
        if ($request->user_to) {
            $query->where('to', 'LIKE', '%' . $request->user_to . '%');
        }
        if ($request->deliver_status) {
            $query->where('delivery_status', 'LIKE', '%' . $request->deliver_status . '%');
        }
        if ($request->date) {
            $query->where('created_at', 'LIKE', '%' . $request->date . '%');
        }
    
        $twilioDeliveryLogs = $query->paginate(50);
    
        return view('twilio.delivery_logs', compact('twilioDeliveryLogs','twiliCoustomerEmails'));
    }

    /**
     * This function is use for handling message delivery status. webhook call for twilio send message
     *
     * @param  int  $cid
     * @param  int  $marketingMessageCId
     * @return JsonResponse
     */
    public function handleMessageDeliveryStatus(Request $request, $cid, $marketingMessageCId)
    {
        try {
            // Check customer Id is present or not
            if (empty($cid)) {
                throw new Exception('Required parameters are missing');
            }

            // Check marketing message customer Id is present or not
            if (empty($marketingMessageCId)) {
                throw new Exception('Required parameters are missing');
            }

            // Store webhook response in database
            TwilioMessageDeliveryLogs::create([
                'marketing_message_customer_id' => $marketingMessageCId,
                'customer_id' => $cid,
                'account_sid' => $request->input('AccountSid'),
                'message_sid' => $request->input('MessageSid'),
                'to' => $request->input('To'),
                'from' => $request->input('From'),
                'delivery_status' => $request->input('MessageStatus'),
                'api_version' => $request->input('ApiVersion'),
            ]);
        } catch (\Exception $e) {
            \Log::info('handleMessageDeliveryStatus customer id -> ' . $cid);
            \Log::info('handleMessageDeliveryStatus marketing message customer id -> ' . $marketingMessageCId);
            \Log::info('handleMessageDeliveryStatus AccountSid -> ' . $request->input('AccountSid'));
            \Log::info('handleMessageDeliveryStatus MessageSid -> ' . $request->input('MessageSid'));
            \Log::info('handleMessageDeliveryStatus twilio webhook error -> ' . $e->getMessage());
        }
    }

    /**
     * This function is used to ask customer to leave a message
     *
     * @return XmlResponse
     */
    public function handleIncomingCall()
    {
        $response = new VoiceResponse();
        $gather = $response->gather(
            [
                'input' => 'speech dtmf',
                'numDigits' => 1,
                'action' => route('twilio_menu_response', [], false),
            ]
        );

        $gather->say(
            'Currently all lines are busy. Please press 1 for a leave a message',
            ['loop' => 3]
        );

        return $response;
    }

    /**
     * This function is used to check URL is exist or not
     *
     * @param  string  $url
     * @return bool
     */
    public function checkUrlExists($url)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $result = curl_exec($ch);

        // Check result is not empty
        if ($result !== false) {
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $status_code == 200;
        }

        curl_close($ch);
        LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($result), $httpcode, \App\Http\Controllers\TwilioController::class, 'twilioCallForward');

        return false;
    }

    public function StatusColourUpdate(request $request)
    {
            $formData = $request->input('formData');
        
            if (is_array($formData)) {
                foreach ($formData as $data) {
                    // Search for an existing record based on status_name
                    $statusColor = TwilioConditionStatus::firstOrCreate([
                        'status_name' => $data['status'],
                    ]);
        
                    // Update the existing record with the new color code
                    $statusColor->color = $data['colorCode'];
                    $statusColor->save();
                }
        
                // Return a success response if needed
                return response()->json(['message' => 'Status colors saved successfully']);
            }
        
            // Return an error response if formData is not an array
            return response()->json(['error' => 'Invalid form data']);
        
    }
}
