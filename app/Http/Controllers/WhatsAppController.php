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
		$from = $data['from'];
		$text = $data['text'];
		$lead = $this->findLeadByNumber( $from );
		$message = ChatMessage::create( [
			'lead_id' => $lead->id,
			'number' => $from,
			'message' => $text
		] );
        return response("");
    }
    /**
     * Send message
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request, $context)
    {
	   $data = $request->json()->all();
       try {
           if ($context == "leads") {
             $lead = Leads::findOrFail( $data['lead_id'] );
             $this->sendWithWhatsApp( $lead->contactno, $data['message'] );
             ChatMessage::create([
                'lead_id' => $lead->id,
                'number' => NULL,
                'message' => $data['message']
               ]);
            } elseif ($context == "orders") {
             $order = Order::findOrFail( $data['order_id'] );
             $this->sendWithWhatsApp( $order->contact_detail, $data['message'] );
             ChatMessage::create([
                'order_id' => $order->id,
                'number' => NULL,
                'message' => $data['message']
               ]);
            }
        } catch (\Exception $ex) {
            return response($ex->getMessage(), 500);
        }
       return response("");
    }
	/**
     * poll messages
     *
     * @return \Illuminate\Http\Response
     */
    public function pollMessages(Request $request, $context)
    {
        /*
	   $sample = [
			[
				'id' => 1,
				'message' => 'Hello 123',
				'received' => TRUE,
				'lead_id' => $leadId
			],
			[
				'id' => 2,
				'message' => 'Response 123',
				'received' => FALSE,
				'lead_id' => $leadId
			]
		];
		return response()->json($sample);
        */
				
	   $elapse = (int) $request->get("elapse");
	   $date = new \DateTime;
	   $date->modify(sprintf("-%s seconds", $elapse));
       $params = [];
       if ($context == "leads") {
            $id = $request->get("leadId");
            $params['lead_id'] = $id;
	        $messages = ChatMessage::where('created_at', '>=', $date->format('Y-m-d H:i:s'))
                ->where('lead_id', '=', $id);
       } elseif ($context == "orders") {
            $id = $request->get("orderId");
            $params['order_id'] = $id;
	        $messages = ChatMessage::where('created_at', '>=', $date->format('Y-m-d H:i:s'))
                ->where('order_id', '=', $id);
        }
	   $result = [];
	   foreach ($messages->get() as $message) {
         $received = false;
         if (!is_null($message['number'])) {
            $received = true;
         }
	     $result[] = array_merge($params, [
                'id' => $message['id'],
                'received' =>$received,
                'message' => $message['message'],
                'number' => $message['number']
            ]);
	   }
       return response()->json( $result );
    }

	private function sendWithWhatsApp($number, $text)
	{
        $curl = curl_init();
        $keys = \Config::get("apiwha.api_keys");
        $key = $keys[0]['key']; 
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
}
