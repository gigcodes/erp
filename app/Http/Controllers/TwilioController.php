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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers;


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
       $response = new Twiml(); 
       $this->createIncomingGather($response, "thank you for calling solo luxury. Please dial 1 for sales 2 for support 3 for other queries");
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
        if ($digits === "1") {  
            $this->dialAllClients($response, "sales", $context, $object);
        } else if ($digits == "2") {
            $this->dialAllClients($response, "support", $context, $object);
        } else if ($digits == "3") {
            $this->dialAllClients($response, "queries", $context, $object);
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
       $context = $request->get("context");
       $id = $request->get("internalId");
       $response = new Twiml(); 
       $response->dial( $number, [
            'callerId' => \Config::get("twilio.caller_id"),
            'record' => 'true',
            'recordingStatusCallback' => \Config::get("app.url")."/twilio/recordingStatusCallback?context=" . $context . "&amp;internalId=" .  $id
        ]);
       return \Response::make((string) $response, '200')->header('Content-Type', 'text/xml');
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
        CallRecording::create($params);
    }
    private function getConnectedClients($role="")
    {
        $users = User::get();
        $clients = [];
        foreach ($users as $user) {
            if ($user->agent_role == $role) {
                $clients[] = $user->name;
            }
        }
        return $clients;
    }
    private function dialAllClients($response, $role="sales", $context=NULL, $object=NULL)
    {
        $url =  \Config::get("app.url")."/twilio/recordingStatusCallback";
        if ($context) {
            $url =  \Config::get("app.url")."/twilio/recordingStatusCallback?context=" . $context . "&internalId=" .  $object->id;
        }

        $dial = $response->dial([
            'record' => 'true',
            'recordingStatusCallback' =>$url
        ]);
        $clients = $this->getConnectedClients($role);
        foreach ($clients as $client) {
            $dial->client( $client );
        }
    }
   private function createIncomingGather($response, $speech)
    {
        $gather = $response->gather([
            'action' => url("/twilio/gatherAction")
        ]);
        $gather->say($speech);
    }
}
