<?php

/**
 * Class TwilioController | app/Http/Controllers/TwilioController.php
 * Twilio integration for VOIP purpose using Twilio's Voice REST API
 *
 * @package  Twillio
 * @subpackage Jwt Token
 * @filesource required php 7 as this file contains tokenizer extension which was not stable prior to this version
 * @see https://www.twilio.com/docs/voice/quickstart/php
 * @see FindByNumberController
 * @author   sololux <sololux@gmail.com>
 */

namespace App\Http\Controllers;

use App\StoreWebsite;
use App\StoreWebsiteTwilioNumber;
use App\TwilioActiveNumber;
use App\TwilioCallForwarding;
use App\TwilioCredential;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
use App\Category;
use App\Notification;
use App\Leads;
use App\Status;
use App\Setting;
use App\User;
use App\Brand;
use App\Product;
use App\Customer;
use App\Message;
use App\CallRecording;
use App\CallBusyMessage;
use App\CallHistory;
use App\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers;
use App\Recording;
use Carbon\Carbon;
use Response;
use App\Helpers\TwilioHelper;
use Twilio\TwiML\VoiceResponse;

/**
 * Class TwilioController - active record
 * 
 * A Twillio class which is extending FindBYNumber controller class
 * This class is used to make and receive phone calls with Twilio Programmable Voice.
 *
 * @package  Twiml
 * @subpackage Jwt Token
 */
class TwilioController extends FindByNumberController
{

    /**
     * Twillio Account SID and Auth Token from twilio.com/console
     * Initilizing the Twilio client
     * @access private
     * @todo Function is not used anywhere.
     * @return Twilio Object
     *
     * @uses Client
     * @uses Config
     */
    private function getTwilioClient()
    {
        return new Client(\Config::get("twilio.account_sid"), \Config::get("twilio.auth_token"));
    }

    /**
     * Create a token for the twilio device which expires after 1 min
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio/token")
     *
     * @uses Auth
     * @uses ClientToken
     */
    public function createToken(Request $request)
    {
        if (\Auth::check()) {
            $user = \Auth::user();
            $agent = str_replace('-', '_', str_slug($user->name));
            $capability = new ClientToken(\Config::get("twilio.account_sid"), \Config::get("twilio.auth_token"));
            $capability->allowClientOutgoing(\Config::get("twilio.webrtc_app_sid"));
            $capability->allowClientIncoming($agent);
            $expiresIn = (3600 * 1);
            $token = $capability->generateToken();
            return response()->json(['twilio_token' => $token, 'agent' => $agent]);
        }
    }

    /**
     * Incoming call URL for Twilio programmable voice
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio/incoming")
     *
     * @uses Log
     * @uses Twiml
     */
    public function incomingCall(Request $request)
    {
        $number = $request->get("From");
        //$number = '919748940238';

        Log::info('Enter in Incoming Call Section ');
        $response = new Twiml();
        list($context, $object) = $this->findCustomerOrLeadOrOrderByNumber(str_replace("+", "", $number));
        if (!$context) {
            $reject = $response->reject([
                'reason' => 'Busy'
            ]);
            return Response::make((string)$response, '200')->header('Content-Type', 'text/xml');
        }

        $dial = $response->dial([
            'record' => 'true',
            'recordingStatusCallback' => \Config::get("app.url") . "/twilio/recordingStatusCallback?context=" . $context . "&amp;internalId=" . $object->id
        ]);

        $clients = $this->getConnectedClients();
        /** @var Helpers $client */
        foreach ($clients as $client) {
            $dial->client($client);
        }
        return \Response::make((string)$response, '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Incoming IVR
     * @param Request $request Request
     * @return \Illuminate\Http\Response
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
        Log::info('Showing user profile for IVR: ');

        $number = $request->get("From");
        list($context, $object) = $this->findCustomerOrLeadOrOrderByNumber(str_replace("+", "", $number));
        $url = \Config::get("app.url") . "/twilio/recordingStatusCallback";
        $actionurl = \Config::get("app.url") . "/twilio/handleDialCallStatus";

        if ($context) {
            $url = \Config::get("app.url") . "/twilio/recordingStatusCallback?context=" . $context . "&internalId=" . $object->id . "&Mobile=" . $number;
        }

        $response = new Twiml();

        $time = Carbon::now();
        $saturday = Carbon::now()->endOfWeek()->subDay();
        $sunday = Carbon::now()->endOfWeek();
        $morning = Carbon::create($time->year, $time->month, $time->day, 10, 0, 0);
        $evening = Carbon::create($time->year, $time->month, $time->day, 17, 30, 0);

        if (($context == "customers" && $object->is_blocked == 1) || Setting::get('disable_twilio') == 1) {
            $response = $response->reject();
        } else {
            if ($time == $sunday || $time == $saturday) { // If Sunday or Holiday
                $response->play(\Config::get("app.url") . "/holiday_ring.mp3");
            } elseif (!$time->between($morning, $evening, true)) {
                $response->play(\Config::get("app.url") . "/end_work_ring.mp3");
            } else {
                $response->play(\Config::get("app.url") . "/intro_ring.mp3");

                $dial = $response->dial([
                    'record' => 'true',
                    'recordingStatusCallback' => $url,
                    'action' => $actionurl,
                    'timeout' => '60'
                ]);

                $clients = $this->getConnectedClients();

                Log::info('Client for callings: ' . implode(',', $clients));
                /** @var Helpers $client */
                foreach ($clients as $client) {
                    $dial->client($client);
                }
            }
        }


// $response->say("Greetings & compliments of the day from solo luxury. the largest online shopping destination where your class meets authentic luxury for your essential pleasures. Your call will be answered shortly.");


        /* -------------------------------------------------------- */


        // $response = new Twiml();
        // $this->createIncomingGather($response, "thank you for calling solo luxury. Please dial 1 for sales 2 for support 3 for other queries");
        // $response = new Twiml();
        // $this->createIncomingGather($response, "Thank you for calling solo luxury. Please dial 1 for sales, 2 for support or 3 for other queries");

        return \Response::make((string)$response, '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Gather action
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio/gatherAction")
     *
     * @uses Log
     * @uses Twiml
     * @uses Config
     */
    public function gatherAction(Request $request)
    {

        $response = new Twiml();
        Log::info(' TIME CHECKING : 2');

        $digits = trim($request->get("Digits"));
        Log::info(' TIME CHECKING : 3');

        $clients = [];

        $number = $request->get("From");
        Log::info(' TIME CHECKING : 4');

        // list($context, $object) = $this->findLeadOrOrderByNumber(str_replace("+", "", $number));
        $recordurl = \Config::get("app.url") . "/twilio/storerecording";
        // Log::info('Context: '.$context);
        Log::info(' TIME CHECKING : 5');

        if ($digits === "0") {
            Log::info(' Enterd into Leave a message section');
            $response->record(
                ['maxLength' => '20',
                    'method' => 'GET',
                    'action' => route('hangup', [], false),
                    'transcribeCallback' => $recordurl
                ]
            );

            $response->say(
                'No recording received. Goodbye',
                ['voice' => 'alice', 'language' => 'en-GB']
            );
            $response->hangup();
            return $response;
        } else {
            $this->createIncomingGather($response, "We did not understand that input.");
        }


        return \Response::make((string)$response, '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Outgoing call URL
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio/outgoing")
     *
     * @uses Log
     * @uses Twiml
     * @uses Config
     */
    public function outgoingCall(Request $request)
    {
        Log::info('Call Status: = ' . $request->get("CallStatus"));

        $number = $request->get("PhoneNumber");
        Log::info('Call SID: ' . $request->get("CallSid"));
        $context = $request->get("context");
        $id = $request->get("internalId");

        if ($request->get("CallNumber") != null) {
            $callFrom = $request->get("CallNumber");
        } else {
            $callFrom = \Config::get("twilio.default_caller_id");
        }

        $actionurl = \Config::get("app.url") . "/twilio/handleOutgoingDialCallStatus" . "?phone_number=$number";
        Log::info('Outgoing call function Enter ' . $id);
        $response = new Twiml();
        $response->dial($number, [
            'callerId' => $callFrom,
            'record' => 'true',
            'recordingStatusCallback' => \Config::get("app.url") . "/twilio/recordingStatusCallback?context=" . $context . "&internalId=" . $id . "&Mobile=" . $number,
            'action' => $actionurl
        ]);
        $recordurl = \Config::get("app.url") . "/twilio/storetranscript";
        Log::info('Trasncript Call back url ' . $recordurl);
        $response->record(['transcribeCallback' => $recordurl]);

        return \Response::make((string)$response, '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Outgoing Conference call URL
     * @param Request $request Request
     * @return \Illuminate\Http\Response
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
        $sid = \Config::get("twilio.account_sid");
        $token = \Config::get("twilio.auth_token");
        $twilio = new Client($sid, $token);


        foreach ($to as $number) {
            $participant = $twilio->conferences(\Config::get("twilio.conference_sid"))
                ->participants
                ->create($from, $number);
            $caller_sid = $participant->callSid;
            $details[] = array('number' => $number, 'sid' => $caller_sid);

        }

        // Via a request instance...
        return \Response::make($details, '200')->header('Content-Type', 'text/xml');

    }

    /**
     * Mute Number From Conference
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio-conference-mute")
     *
     * @uses Log
     * @uses Config
     */
    public function muteConferenceNumber(Request $request)
    {
        $caller_sid = $request->sid;
        $participant = $twilio->conferences(\Config::get("twilio.conference_sid"))
            ->participants($caller_sid)
            ->update(array("muted" => True));
        // Via a request instance...
        return \Response::make('Muted SucessFully', '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Hold Number From Conference
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio-conference-hold")
     *
     * @uses Log
     * @uses Config
     */
    public function holdConferenceNUmber(Request $request)
    {
        $caller_sid = $request->sid;
        $participant = $twilio->conferences(\Config::get("twilio.conference_sid"))
            ->participants($caller_sid)
            ->update(array("muted" => True));
        // Via a request instance...
        return \Response::make('Hold SucessFully', '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Remove Number From Conference
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio-conference-remove")
     *
     * @uses Log
     * @uses Config
     */
    public function removeConferenceNumber(Request $request)
    {
        $caller_sid = $request->sid;
        $participant = $twilio->conferences(\Config::get("twilio.conference_sid"))
            ->participants($caller_sid)
            ->update(array("muted" => True));
        // Via a request instance...
        return \Response::make('Number Removed SucessFully', '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Store a new Trasnscript from call
     * @param Request $request Request
     * @return string
     * @Rest\Post("twilio/storetranscript")
     *
     * @uses Log
     * @uses CallRecording
     */
    public function storetranscript(Request $request)
    {
        Log::info('---------------- Enter in Function for Trasncript--------------------- ' . $request->get("CallStatus"));
        $sid = $request->get("CallSid");
        Log::info('TranscriptionText ' . $request->input('TranscriptionText'));

        $call_status = $request->get("CallStatus");
        if ($call_status == 'completed') {


            CallRecording::where('callsid', $sid)
                ->first()
                ->update(['message' => $request->input('TranscriptionText')]);
        }
        return 'Ok';
    }

    /**
     * Outgoing call URL
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Get("twilio/getLeadByNumber")
     *
     * @uses Customer
     */
    public function getLeadByNumber(Request $request)
    {
        $number = $request->get("number");

        list($context, $object) = $this->findCustomerOrLeadOrOrderByNumber(str_replace("+", "", $number));
        if (!$context) {
            $customer = new Customer;

            $customer->name = 'Customer from Call';
            $customer->phone = str_replace("+", "", $number);
            $customer->rating = 1;

            $customer->save();

            return response()->json(['found' => FALSE, 'number' => $number]);
        }
        if ($context == "leads") {
            $result = ['found' => TRUE, 'name' => $object->client_name, 'customer_id' => \Config::get("app.url") . '/customer/' . $object->customer_id, 'customer_url' => route('customer.show', $object->customer_id)];
        } elseif ($context == "orders") {
            $result = ['found' => TRUE, 'name' => $object->client_name, 'customer_url' => route('customer.show', $object->customer_id)];
        } elseif ($context == "customers") {
            $result = ['found' => TRUE, 'name' => $object->name, 'customer_url' => route('customer.show', $object->id)];
        }
        return response()->json($result);
    }

    /**
     * Recording status callback
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio/recordingStatusCallback")
     * @return void
     *
     * @uses CallRecording
     */
    public function recordingStatusCallback(Request $request)
    {

        $url = $request->get("RecordingUrl");
        $sid = $request->get("CallSid");
        $params = [
            'recording_url' => $url,
            'twilio_call_sid' => $sid,
            'callsid' => $sid
        ];
        $context = $request->get("context");
        $internalId = $request->get("internalId");

        if ($context && $internalId) {
            if ($context == "leads") {
                $params['lead_id'] = $internalId;
            } elseif ($context == "orders") {
                $params['order_id'] = $internalId;
            } elseif ($context == "customers") {
                $params['customer_id'] = $internalId;
            }
        }
        $customer_mobile = $request->get("Mobile");
        if ($customer_mobile != '')
            $params['customer_number'] = $customer_mobile;

        CallRecording::create($params);
    }

    /**
     * Get data of connected clients
     * @access private
     * @param Role $role
     * @return array $clients
     *
     * @uses Helpers
     * @uses User
     *
     * @todo static user id's are passed and role is given
     */
    private function getConnectedClients($role = "")
    {
        $hods = Helpers::getUsersByRoleName('HOD of CRM');
        $andy = User::find(56);
        $yogesh = User::find(6);
        $clients = [];
        /** @var Helpers $hod */
        foreach ($hods as $hod) {
            $clients[] = str_replace('-', '_', str_slug($hod->name));
        }

        if (Setting::get('incoming_calls_andy') == 1) {
            $clients[] = str_replace('-', '_', str_slug($andy->name));
        }

        if (Setting::get('incoming_calls_yogesh') == 1) {
            $clients[] = str_replace('-', '_', str_slug($yogesh->name));
        }

        return $clients;
    }

    /**
     * Dial all clients
     * @access private
     * @param $response
     * @param $role
     * @param $context
     * @param $object
     * @param $number
     * @return void
     *
     * @uses Config
     * @uses Log
     * @todo not in use currently
     */
    private function dialAllClients($response, $role = "sales", $context = NULL, $object = NULL, $number = "")
    {
        $url = \Config::get("app.url") . "/twilio/recordingStatusCallback";
        $actionurl = \Config::get("app.url") . "/twilio/handleDialCallStatus";
        if ($context) {
            $url = \Config::get("app.url") . "/twilio/recordingStatusCallback?context=" . $context . "&internalId=" . $object->id . "&Mobile=" . $number;
        }


        $dial = $response->dial([
            'record' => 'true',
            'recordingStatusCallback' => $url,
            'action' => $actionurl,
            'timeout' => 5
        ]);

        $clients = $this->getConnectedClients($role);

        Log::info('Client for callings: ' . implode(',', $clients));
        /** @var Helpers $client */
        foreach ($clients as $client) {
            $dial->client($client);
        }
    }

    /**
     * Incoming calls gathering
     * @access private
     * @param Object $response
     * @param $speech
     * @uses Config
     *
     * @return void
     */
    private function createIncomingGather($response, $speech)
    {
        $gather = $response->gather([
            'action' => url("/twilio/gatherAction")
        ]);
        $gather->play(\Config::get("app.url") . "/busy_ring.mp3");
    }

    /**
     * Handle Dial call callback
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio/handleDialCallStatus")
     * @uses CallHistory
     * @uses Customer
     * @uses Log
     */
    public function handleDialCallStatus(Request $request)
    {
        $response = new Twiml();
        $callStatus = $request->input('DialCallStatus');
        $recordurl = \Config::get("app.url") . "/twilio/storerecording";
        Log::info('Current Call Status ' . $callStatus);

        if ($callStatus === 'completed') {
            $recordurl = \Config::get("app.url") . "/twilio/storetranscript";
            Log::info('Trasncript Call back url ' . $recordurl);
            $response->record(['transcribeCallback' => $recordurl]);
        } else {
            $params = [
                'twilio_call_sid' => $request->input('Caller'),
                'message' => 'Missed Call',
                'caller_sid' => $request->input('CallSid')
            ];

            CallBusyMessage::create($params);
            Log::info(' Missed Call saved');
            Log::info('-----SID----- ' . $request->input('CallSid'));

            $this->createIncomingGather($response, "Please dial 0 for leave message");
        }

        if ($customer = Customer::where('phone', 'LIKE', str_replace('+91', '', $request->input('Caller')))->first()) {
            $params = [
                'customer_id' => $customer->id,
                'status' => $callStatus
            ];

            CallHistory::create($params);
        }


        return $response;
    }

    /**
     * Handle Dial call callback
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio/handleOutgoingDialCallStatus")
     * @uses CallHistory
     * @uses Customer
     * @uses ChatMessage
     * @uses Log
     */
    public function handleOutgoingDialCallStatus(Request $request)
    {
        $response = new Twiml();
        $callStatus = $request->input('DialCallStatus');
        Log::info('Current Outgoing Call Status ' . $callStatus);
        Log::info($request->all());

        if ($callStatus == 'busy' || $callStatus == 'no-answer') {
            if ($customer = Customer::where('phone', $request->phone_number)->first()) {
                $params = [
                    'number' => NULL,
                    'message' => 'Greetings from Solo Luxury, our Solo Valets were trying to get in touch with you but were unable to get through, you can call us on 0008000401700. Please do not use +91 when calling  as it does not connect to our toll free number.',
                    'customer_id' => $customer->id,
                    'approved' => 1,
                    'status' => 2
                ];

                ChatMessage::create($params);

                app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $params['message']);
            }
        }

        if ($customer = Customer::where('phone', 'LIKE', str_replace('+91', '', $request->phone_number))->first()) {
            $params = [
                'customer_id' => $customer->id,
                'status' => $callStatus
            ];

            CallHistory::create($params);
        }

        return $response;
    }

    /**
     * Store a new recording from callback
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio/storerecording")
     * @uses CallBusyMessage
     */
    public function storeRecording(Request $request)
    {


        $params = [
            'recording_url' => $request->input('RecordingUrl'),
            'twilio_call_sid' => $request->input('Caller'),
            'message' => $request->input('TranscriptionText')
        ];

        $exist_call = CallBusyMessage::where('caller_sid', '=', $request->input('CallSid'))->first();
        if ($exist_call) {
            CallBusyMessage::where('caller_sid', $request->input('CallSid'))
                ->first()
                ->update($params);
            Log::info('update call busy recording table');
        } else {

            Log::info('Recording URL' . $request->input('RecordingUrl'));
            Log::info('Caller NAME ' . $request->input('From'));
            Log::info('-----SID----- ' . $request->input('CallSid'));
            CallBusyMessage::create($params);
            Log::info('insert new call busy recording table');
        }
    }

    /**
     * Replies with a hangup
     *
     * @return \Illuminate\Http\Response
     * @Rest\Post("/twilio/hangup")
     */
    public function showHangup()
    {
        $response = new Twiml();
        $response->say(
            'Thanks for your message. Goodbye',
            ['voice' => 'alice', 'language' => 'en-GB']
        );
        $response->hangup();

        return $response;
    }

    public function getTwilioActiveNumbers()
    {
        try {
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/IncomingPhoneNumbers.json?Beta=false&PageSize=50&Page=0';
            $result = TwilioHelper::fetchNumbers($url, $sid, $token);
            $result = json_decode($result);
            if (count($result->incoming_phone_numbers) > 0) {
                $this->saveNumber($result->incoming_phone_numbers);
            }
            if ($result->end > 0) {
                for ($i = 1; $i <= $result->end; $i++) {
                    $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/IncomingPhoneNumbers.json?Beta=false&PageSize=50&Page=' . $i;
                    $result = TwilioHelper::fetchNumbers($url, $sid, $token);
                    $result = json_decode($result);
                    if (count($result->incoming_phone_numbers) > 0) {
                        $this->saveNumber($result->incoming_phone_numbers);
                    }
                }
            }
            echo 'Number Saved successfully';
        } catch (\Exception $e) {
            print_r($e->getMessage());
            die;
        }

    }

    public function saveNumber($incoming_phone_numbers)
    {
        try {
            foreach ($incoming_phone_numbers as $numbers) {
                try {
                    //check if no. already exists then update
                    $find_number = TwilioActiveNumber::where('phone_number', '=', $numbers->phone_number)->firstOrFail();
                } catch (\Exception $e) {
                    TwilioActiveNumber::create([
                        'sid' => $numbers->sid,
                        'account_sid' => $numbers->account_sid,
                        'friendly_name' => $numbers->friendly_name,
                        'phone_number' => $numbers->phone_number,
                        'voice_url' => $numbers->voice_url,
                        'date_created' => $numbers->date_created,
                        'date_updated' => $numbers->date_updated,
                        'sms_url' => $numbers->sms_url,
                        'voice_receive_mode' => $numbers->voice_receive_mode,
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
                    ]);
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function manageNumbers(Request $request)
    {
        try {
            $twilio_accounts = TwilioCredential::where(['status' => true])->get();
            if(count($twilio_accounts) > 0){
                //get all numbers of twilio
                $id = $request->get('id');
                if($id != null){
                    try {
                        $check_account = TwilioCredential::where(['id' => $id])->firstOrFail();
                        $sid = $check_account->account_id;
                        $numbers = TwilioActiveNumber::where('account_sid', '=', $sid)->with('assigned_stores.store_website')->get();
                        $store_websites = StoreWebsite::all();
                        return view('twilio.manage-numbers', compact('numbers', 'store_websites','twilio_accounts','sid'));
                    } catch (\Exception $e) {
                        return redirect('/twilio/manage-numbers')->WithErrors(['Undefined id']);
                    }
                }

                return view('twilio.manage-numbers', compact('twilio_accounts'));

            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function assignTwilioNumberToStoreWebsite(Request $request)
    {
        //check if same store website contains another number then delete
        try {
            StoreWebsiteTwilioNumber::where(['store_website_id' => $request->store_website_id])->delete();
            //create new record
            $assign_number = StoreWebsiteTwilioNumber::create([
                'store_website_id' => $request->store_website_id,
                'twilio_active_number_id' => $request->twilio_number_id
            ]);
            return new JsonResponse(['data' => $assign_number, 'message' => 'Number assigned to store website successfully', 'status' => 1]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage(), 'status' => 0]);

        }

    }

    public function CallRecordings(Request $request)
    {
        try {
            $id = $request->get('id');
            $check_account = TwilioCredential::where(['id' => $id])->firstOrFail();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio = new Client($sid, $token);
            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Recordings.json?__referrer=runtime&Format=json&PageSize=100&Page=0';
            $result = TwilioHelper::fetchNumbers($url, $sid, $token);
            $result = json_decode($result);
            return view('twilio.manage-recordings', compact('result'));
        } catch (\Exception $e) {
            return redirect('twilio/manage-numbers')->withErrors(['Undefined twilio account']);
        }

    }

    public function downloadRecording(Request $request, $recording_id)
    {
            $id = $request->get('id');
            $check_account = TwilioCredential::where(['id' => $id])->firstOrFail();
            $sid = $check_account->account_id;
            $file = 'https://api.twilio.com/2010-04-01/Accounts/'.$sid.'/Recordings/'.$recording_id.'.mp3';
            header("Content-type: application/x-file-to-save");
            header("Content-Disposition: attachment; filename=".basename($file));
            readfile($file);
            exit;
    }

    public function callForwarding(Request $request)
    {
        try {
            $id = $request->twilio_account_id;
            $check_account = TwilioCredential::where(['id' => $id])->first();
            $sid = $check_account->account_id;
            $token = $check_account->auth_token;
            $twilio_number_id = $request->twilio_number_id;
            $new_forwarded_no = $request->area_code.''.$request->phone_no;
            $base_url = env('APP_URL');
            //get number details
            $number_details = TwilioActiveNumber::where('id',$twilio_number_id)->first();
            //update webhook url on twilio console using api
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.twilio.com/2010-04-01/Accounts/'.$sid.'/IncomingPhoneNumbers/'.$number_details->sid.'.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            //curl_setopt($ch, CURLOPT_POSTFIELDS,"VoiceUrl=http://5be3e7a64b37.ngrok.io/run-webhook/".$number_details->sid."");
            curl_setopt($ch, CURLOPT_POSTFIELDS,"VoiceUrl=".$base_url."/run-webhook/".$number_details->sid."");
            curl_setopt($ch, CURLOPT_USERPWD, $sid . ':' . $token);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            //delete old forwarding
            TwilioCallForwarding::where('twilio_number_sid','=',$number_details->sid)->delete();

            TwilioCallForwarding::create([
               'twilio_number_sid' => $number_details->sid,
               'twilio_number' => $number_details->phone_number,
               'forwarding_on' => $new_forwarded_no
            ]);

            return redirect()->back()->with('success','Call forwarded successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function runWebhook($sid)
    {
        Log::info('Webhook called successfully');
        $twiml = new VoiceResponse();
        //get forwarded no. of this twilio_sid
        $forwarding = TwilioCallForwarding::where('twilio_number_sid','=',$sid)->first();
        Log::info('forwarding number details '.$forwarding->forwarding_on);
        Log::info('number dialled');
        $twiml->say("Please wait , we are connecting your call");
        $twiml->dial($forwarding->forwarding_on, ['record' => 'record-from-ringing-dual']);
        $twiml->hangup();
        echo $twiml;
        die;
    }



}
