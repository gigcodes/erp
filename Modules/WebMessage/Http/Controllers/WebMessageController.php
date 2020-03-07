<?php

namespace Modules\WebMessage\Http\Controllers;

use App\ChatMessage;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class WebMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        // customer list need to display first
        // show last customer message sent
        // on click based show the customer message
        $customerList = $this->getLastConversationGroup();

        $customers = [];

        $customerIds    = [];
        $lastMessageIds = [];
        if (!empty($customerList)) {
            foreach ($customerList as $customer) {
                $customerIds[]    = $customer->customer_id;
                $lastMessageIds[] = $customer->last_chat_id;
            }
        }

        // get customer info
        $customerInfo = Customer::getInfoByIds(
            $customerIds,
            ["id", "name", "gender", "email", "phone", "whatsapp_number", "broadcast_number", "created_at"],
            true
        );

        // get last message list
        $messageInfo = ChatMessage::getInfoByIds(
            $lastMessageIds,
            ["id", "number", "message", "media_url", "customer_id", "is_chatbot", "status", "created_at"],
            true
        );

        // check last message has any media images
        $lastImages = ChatMessage::getGroupImagesByIds(
            $lastMessageIds,
            true
        );

        // setup the customer information
        $jsonCustomer = [];
        if (!empty($customerInfo)) {
            foreach ($customerInfo as $customer) {
                $id                              = $customer["id"];
                $customers[$id]["customer_info"] = $customer;

                // json customer setup
                $jsonCustomer[] = [
                    "id"       => $id,
                    "name"     => $customer["name"],
                    "number"   => $customer["phone"],
                    "pic"      => "https://via.placeholder.com/400x400",
                    "lastSeen" => $customer["created_at"],
                ];
            }
        }

        // setup the last message inforation
        $lastMessage = [];
        $jsonMessage = [];
        if (!empty($messageInfo)) {
            foreach ($messageInfo as $message) {
                $id                                                = $message["customer_id"];
                $customers[$id]["last_message_info"]               = $message;
                $customers[$id]["last_message_info"]["has_images"] = false;
                $lastMessage[$message["id"]]                       = $id;

                $jsonMessage[] = [
                    "id"          => $message["id"],
                    "sender"      => 0,
                    "body"        => $message["message"],
                    "time"        => date("M d, Y H:i:s", strtotime($message["created_at"])),
                    "status"      => $message["status"],
                    "recvId"      => $message["customer_id"],
                    "recvIsGroup" => false,
                    "isSender"    => is_null($message["number"]) ? true : false,
                ];
            }
        }

        // last images
        if (!empty($lastImages)) {
            foreach ($lastImages as $lastImg) {
                $customerId = isset($lastMessage[$lastImg->mediable_id]) ? $lastMessage[$lastImg->mediable_id] : 0;
                if ($customerId > 0) {
                    $customers[$customerId]["last_message_info"]["has_images"]     = true;
                    $customers[$customerId]["last_message_info"]["has_images_ids"] = $lastImg->image_ids;
                }
            }
        }

        return view('webmessage::index', compact('customers', 'jsonCustomer', 'jsonMessage'));
    }

    public function getLastConversationGroup($page = 1)
    {
        $customers = \DB::table("chat_messages")
            ->whereNotIn("status", ChatMessage::AUTO_REPLY_CHAT)
            ->groupBy("customer_id")
            ->select(["customer_id", \DB::raw("max(id) as last_chat_id")])
            ->havingRaw("customer_id is not null")
            ->latest()
            ->get();

        return $customers;
    }

    public function messageList(Request $request, $id)
    {
        $customer    = Customer::find($id);
        $jsonMessage = [];

        if (!empty($customer)) {
            $messageInfo = ChatMessage::getInfoByCustomerIds(
                [$customer->id],
                ["id", "number", "message", "media_url", "customer_id", "is_chatbot", "status", "created_at"],
                true
            );

            $messageIds = [];
            if (!empty($messageInfo)) {
                foreach ($messageInfo as $message) {
                    $messageIds[]                = $message["id"];
                    $jsonMessage[$message["id"]] = [
                        "id"          => $message["id"],
                        "sender"      => 0,
                        "body"        => is_null($message["message"]) ? "" : $message["message"],
                        "time"        => date("M d, Y H:i:s", strtotime($message["created_at"])),
                        "status"      => $message["status"],
                        "recvId"      => $message["customer_id"],
                        "recvIsGroup" => false,
                        "isSender"    => is_null($message["number"]) || $message["number"] != $customer->phone ? false : true,
                    ];
                }
            }

            // check last message has any media images
            $lastImages = ChatMessage::getGroupImagesByIds(
                $messageIds,
                true
            );

            $allMediaIds = [];
            if (!empty($lastImages)) {
                foreach ($lastImages as $lastImg) {
                    $cMedia = explode(",", $lastImg->image_ids);
                    if (!empty($cMedia)) {
                        $allMediaIds = array_merge($allMediaIds, $cMedia);
                    }
                }
            }

            $allMedias = \Plank\Mediable\Media::whereIn("id", $allMediaIds)->get();
            $urls      = [];
            if (!$allMedias->isEmpty()) {
                foreach ($allMedias as $aMedias) {
                    $urls[$aMedias->id] = [
                        "url"  => $aMedias->getUrl(),
                        "type" => $aMedias->extension,
                    ];
                }
            }

            // last images
            if (!empty($lastImages)) {
                foreach ($lastImages as $lastImg) {
                    $jsonMessage[$lastImg->mediable_id]["has_media"] = true;
                    $mediaId                                         = explode(",", $lastImg->image_ids);
                    if (!empty($mediaId)) {
                        foreach ($mediaId as $mi) {
                            if (isset($urls[$mi])) {
                                $jsonMessage[$lastImg->mediable_id]["media"][] = $urls[$mi];
                            }
                        }
                    }
                }
            }
        }

        $m = [];
        foreach ($jsonMessage as $jMsg) {
            $m[] = $jMsg;
        }

        return response()->json(["code" => 200, "msgs" => $m]);
    }

}
