<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChatMessage;
use App\Customer;
use App\User;

class TwiliochatController extends Controller
{
    public function getTwilioChat()
    {
        $store_websites = [];//StoreWebsite::all();
        $website_stores = [];//WebsiteStore::with('storeView')->get();

        // if (session()->has('chat_customer_id')) {
            //$chatId       = session()->get('chat_customer_id');
            $chat_message = ChatMessage::where('message_application_id', 3)->orderBy("id", "desc")->get();
            //getting customer name from chat
            // $customer       = Customer::findorfail($chatId);
            // $name           = $customer->name;
            $customerInital = '';//substr($name, 0, 1);
            if (count($chat_message) > 0) {
                foreach ($chat_message as $chat) {
                    if ($chat->user_id != 0) {
                        // Finding Agent
                        $agent       = User::where('id', $chat->user_id)->first();
                        $agentInital = substr($agent->name, 0, 1);

                        $chat->message = '<div data-chat-id="' . $chat->id . '" class="d-flex mb-4"><div class="rounded-circle user_inital">' . $agentInital . '</div><div class="msg_cotainer">' . $chat->message . '<span class="msg_time"> ' . \Carbon\Carbon::createFromTimeStamp(strtotime($chat->created_at))->diffForHumans() . '</span></div></div>';

                    } else {
                         $chat->message = '<div data-chat-id="' . $chat->id . '" class="d-flex justify-content-start mb-4"><div class="rounded-circle user_inital">' . $customerInital . '</div><div class="msg_cotainer">' . $chat->message . '<span class="msg_time"> ' . \Carbon\Carbon::createFromTimeStamp(strtotime($chat->created_at))->diffForHumans() . '</span></div></div>';
                    }
                }
            }
            //echo "<pre>";print_r($message);die;
            // $count = CustomerLiveChat::where('seen', 0)->count();
            return view('twilio.chatMessages', compact('chat_message', 'name', 'customerInital', 'store_websites', 'website_stores'));
        // } else {
        //     $count          = 0;
        //     $message        = '';
        //     $customerInital = '';
        //     $name           = '';
        //     return view('twilio.chatMessages', compact('message', 'name', 'customerInital', 'store_websites', 'website_stores'));
        // }
    }
}
