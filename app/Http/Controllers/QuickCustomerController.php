<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Setting;
use Illuminate\Http\Request;

class QuickCustomerController extends Controller
{
    public function index()
    {
        $title         = "Quick Customer";
        $nextActionArr = \DB::table('customer_next_actions')->get();
        return view("quick-customer.index", compact('title', 'nextActionArr'));
    }

    public function records(Request $request)
    {

        $type = $request->get("type", "unread");

        $customer = \App\Customer::query();

        if (empty($type)) {
            $type = "unread";
        }
        if ($type == "unread") {
            $customer = $customer->join("chat_messages_quick_datas as cmqs", function ($q) {
                $q->on("cmqs.model_id", "customers.id")->where("cmqs.model", Customer::class);
            });
            $customer = $customer->join("chat_messages as cm", "cm.id", "cmqs.last_unread_message_id");
        } else if ($type == "last_communicated") {
            $customer = $customer->join("chat_messages_quick_datas as cmqs", function ($q) {
                $q->on("cmqs.model_id", "customers.id")->where("cmqs.model", Customer::class);
            });
            $customer = $customer->join("chat_messages as cm", "cm.id", "cmqs.last_communicated_message_id");
        } else if ($type == "last_received") {
            $customer = $customer->join("chat_messages_quick_datas as cmqs", function ($q) {
                $q->on("cmqs.model_id", "customers.id")->where("cmqs.model", Customer::class);
            });
            $customer = $customer->join("chat_messages as cm", "cm.id", "cmqs.last_communicated_message_id");
        }

        if($request->customer_id != null) {
            $customer = $customer->where("customers.id",$request->customer_id);
        }

        if($request->customer_name != null) {
            $customer = $customer->where("customers.name","like","%".$request->customer_name."%");
        }

        //Setting::get('pagination')

        $customer = $customer->select(["customers.*", "cm.id as message_id", "cm.status as message_status", "cm.message"])->paginate(10);

        $items = [];
        foreach($customer->items() as $item) {
            $item["short_message"] = strlen($item->message) > 20 ? substr($item->message, 0, 20) : $item->message;
            $items[] = $item;
        }

        return response()->json([
            "code"       => 200,
            "data"       => $items,
            "total"      => $customer->total(),
            "pagination" => (string) $customer->appends($request->input())->links(),
            "page"       => $customer->currentPage()
        ]);

    }

}
