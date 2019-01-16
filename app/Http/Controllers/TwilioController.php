<?php

namespace App\Http\Controllers;

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
use App\Message;
use App\CallRecording;
use App\CallBusyMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers;
use App\Recording;


class TwilioController extends FindByNumberController
{
    private function getTwilioClient()
    {
      return new Client(\Config::get("twilio.account_sid"), \Config::get("twilio.auth_token"));
    }
    /**
     * Create a token for the twilio device
     *
     * @return \Illuminate\Http\Response
     */
    public function createToken(Request $request)
    {
      $client = $this->getTwilioClient();
      $user = \Auth::user();
      $agent = $user->name;
      $capability = new ClientToken(\Config::get("twilio.account_sid"), \Config::get("twilio.auth_token"));
      $capability->allowClientOutgoing(\Config::get("twilio.webrtc_app_sid"));
      $capability->allowClientIncoming($agent);
      $expiresIn = (3600*10);
      $token = $capability->generateToken($expiresIn);
      return response()->json(['twilio_token' => $token]);
    }

    /**
     * Incoming call URL for Twilio programmable voice
     *
     * @return \Illuminate\Http\Response
     */
    public function incomingCall(Request $request)
    {
       $number = $request->get("From");
        //$number = '919748940238';

       Log::info('Enter in Incoming Call Section ');
       $response = new Twiml();
       list($context, $object) = $this->findLeadOrOrderByNumber(str_replace("+", "", $number));
       if (!$context) {
        $reject = $response->reject([
            'reason' => 'Busy'
         ]);
         return Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
       }

       $dial = $response->dial([
            'record' => 'true',
            'recordingStatusCallback' => \Config::get("app.url")."/twilio/recordingStatusCallback?context=" . $context . "&amp;internalId=". $object->id
       ]);
   
       $clients = $this->getConnectedClients();
       foreach ($clients as $client) {
        $dial->client( $client );
       }
       return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }
    /**
     * Incoming IVR
     *
     * @return \Illuminate\Http\Response
     */
    public function ivr(Request $request)
    {
                Log::info('Showing user profile for IVR: ');

       $response = new Twiml();
       $this->createIncomingGather($response, "thank you for calling solo luxury. Please dial 1 for sales 2 for support 3 for other queries");

       $response = new Twiml(); 
       $this->createIncomingGather($response, "Thank you for calling solo luxury. Please dial 1 for sales, 2 for support or 3 for other queries");

       return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }

    /**
     * Gather action
     *
     * @return \Illuminate\Http\Response
     */
    public function gatherAction(Request $request)
    {
        $response = new Twiml();
        $digits = trim($request->get("Digits"));
        $clients = [];
        $number = $request->get("From");
        list($context, $object) = $this->findLeadOrOrderByNumber(str_replace("+", "", $number));
   
      Log::info('Context: '.$context);

        if ($digits === "1") {
            $this->dialAllClients($response, "sales", $context, $object , $number);
        } else if ($digits == "2") {
            $this->dialAllClients($response, "support", $context, $object, $number);
        } else if ($digits == "3") {
            $this->dialAllClients($response, "queries", $context, $object, $number);
        } else {
            $this->createIncomingGather($response, "We did not understand that input. Please dial 1 for sales 2 for support 3 for other queries");
        }


        return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }



    /**
     * Outgoing call URL
     *
     * @return \Illuminate\Http\Response
     */
    public function outgoingCall(Request $request)
    {

       $number = $request->get("PhoneNumber");
        //$number = '919748940238';
       Log::info('Mobile number from: '.$number);
       $context = $request->get("context");
       $id = $request->get("internalId");
       Log::info('Outgoing call function Enter '.$id);
       $response = new Twiml();
       $response->dial( $number, [
            'callerId' => \Config::get("twilio.caller_id"),
            'record' => 'true',
            'recordingStatusCallback' => \Config::get("app.url")."/twilio/recordingStatusCallback?context=" . $context . "&internalId=" .$id . "&Mobile=" .$number 
        ]);
       // $recordurl = \Config::get("app.url")."/twilio/storetranscript"; 
       // $response->record([ 'transcribeCallback' => $recordurl
// ]);

       return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
    }

        /**
     * Store a new Trasnscript from call
     *
     * @return \Illuminate\Http\Response
     */
    public function storetranscript(Request $request)
    {

 $params = [
            'recording_url' => $request->input('RecordingUrl'),
            'twilio_call_sid' => $request->input('Caller'),
            'message' => $request->input('TranscriptionText')
        ];
         Log::info('Recording URL'.$request->input('RecordingUrl'));
         Log::info('Caller NAME '.$request->input('From'));
         Log::info('TranscriptionText '.$request->input('TranscriptionText'));
                return "Recording saved";
            }




    /**
     * Outgoing call URL
     *
     * @return \Illuminate\Http\Response
     */
    public function getLeadByNumber(Request $request)
    {
        $number = $request->get("number");
 
        list($context, $object) = $this->findLeadOrOrderByNumber(str_replace("+", "", $number));
        if (!$context) {
           return response()->json(['found' => FALSE,  'number' => $number]);
        }
        if ($context == "leads") {
            $result = ['found' => TRUE, 'name' => $object->client_name];
        } elseif ($context == "orders") {
            $result = ['found' => TRUE, 'name' => $object->client_name];
        }
        return response()->json( $result );
    }

    /**
     * Recording status callback
     *
     * @return \Illuminate\Http\Response
     */
    public function recordingStatusCallback(Request $request)
    {

        $url = $request->get("RecordingUrl");
        $sid = $request->get("CallSid");
        $params = [
            'recording_url' => $url,
            'twilio_call_sid' => $sid
        ];
        $context = $request->get("context");
        $internalId = $request->get("internalId");

        if ($context && $internalId) {
            if ($context == "leads") {
                $params['lead_id'] =$internalId;
            } elseif ($context == "orders") {
                $params['order_id'] =$internalId;
            }
        }
        $customer_mobile = $request->get("Mobile");
        if($customer_mobile != '')
            $params['customer_number'] = $customer_mobile;

        Log::info('Customer number: '.$customer_mobile);  


        CallRecording::create($params);
    }
    private function getConnectedClients($role="")
    {
        $users = User::get();
        $clients = [];
        foreach ($users as $user) {

              $agentrolearr = explode(',',$user->agent_role);
            if (in_array($role, $agentrolearr)) {
                            $clients[] = $user->name;
                        }

            // if ($user->agent_role == $role) {
            //     $clients[] = $user->name;
            // }
        }
        return $clients;
    }
    private function dialAllClients($response, $role="sales", $context=NULL, $object=NULL , $number = "")
    {
          $url =  \Config::get("app.url")."/twilio/recordingStatusCallback". "&Mobile=" .$number ;
          $actionurl =  \Config::get("app.url")."/twilio/handleDialCallStatus";
        if ($context) {
            $url =  \Config::get("app.url")."/twilio/recordingStatusCallback?context=" . $context . "&internalId=" .  $object->id. "&Mobile=" .$number ;
        }


         $dial = $response->dial([

                'record' => 'true',

                    'recordingStatusCallback' =>$url,

                    'action' => $actionurl

                ]);

        $clients = $this->getConnectedClients($role);

        Log::info('Client for callings: '.implode(',', $clients));  
        foreach ($clients as $client) {
            $dial->client( $client);
        }
    }
   private function createIncomingGather($response, $speech)
    {
        $gather = $response->gather([
            'action' => url("/twilio/gatherAction")
        ]);
        $gather->say($speech);
    }

    /**
     * Handle Dial call callback
     *
     * @return \Illuminate\Http\Response
     */

    public function handleDialCallStatus(Request $request){

         $response = new Twiml();
         $callStatus = $request->input('DialCallStatus');
         $recordurl = \Config::get("app.url")."/twilio/storerecording"; 

      if ($callStatus !== 'completed') {
            $response->say(
                'It appears that no agent is available. ' .
                'Please leave a message after the beep',
                ['voice' => 'alice', 'language' => 'en-GB']
            );

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
        }
        return $response;
    }


        /**
     * Store a new recording from callback
     *
     * @return \Illuminate\Http\Response
     */
    public function storeRecording(Request $request)
    {

 $params = [
            'recording_url' => $request->input('RecordingUrl'),
            'twilio_call_sid' => $request->input('Caller'),
            'message' => $request->input('TranscriptionText')
        ];
         Log::info('Recording URL'.$request->input('RecordingUrl'));
         Log::info('Caller NAME '.$request->input('From'));
         Log::info('TranscriptionText '.$request->input('TranscriptionText'));
         CallBusyMessage::create($params);
                return "Recording saved";
            }

 /**
     * Replies with a hangup
     *
     * @return \Illuminate\Http\Response
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



}
