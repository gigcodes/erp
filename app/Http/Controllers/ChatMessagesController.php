<?php

namespace App\Http\Controllers;

use App\Customer;
use App\DeveloperTask;
use App\User;
use App\Vendor;
use App\Supplier;
use App\Task;
use App\Old;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\PublicKey;
use App\SiteDevelopment;
use App\SocialStrategy;
use App\StoreSocialContent;
use App\ChatMessage;
class ChatMessagesController extends Controller
{
    /**
     * Load more messages from chat_messages
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadMoreMessages(Request $request)
    {   
        // Set variables
        $limit = $request->get("limit", 3);
        $loadAttached = $request->get("load_attached", 0);
        $loadAllMessages = $request->get("load_all", 0);

        // Get object (customer, vendor, etc.)
        switch ($request->object) {
            case 'customer':
                $object = Customer::find($request->object_id);
                break;
            case 'user':
                $object = User::find($request->object_id);
                break;
            case 'vendor':
                $object = Vendor::find($request->object_id);
                break;
            case 'task':
                $object = Task::find($request->object_id);
                break;
            case 'developer_task':
                $object = DeveloperTask::find($request->object_id);
                break;
            case 'supplier':
                $object = Supplier::find($request->object_id);
                break;
            case 'old':
                $object = Old::find($request->object_id);
                break;
            case 'site_development':
                $object = SiteDevelopment::find($request->object_id);
                break; 
            case 'social_strategy':
                $object = SocialStrategy::find($request->object_id);
                break; 
            case 'content_management':
                $object = StoreSocialContent::find($request->object_id);
            break;    
            default:
                $object = Customer::find($request->object);
        }

        // Set raw where query
        $rawWhere = "(message!='' or media_url!='')";

        // Do we want all?
        if ($loadAllMessages == 1) {
            $loadAttached = 1;
            $rawWhere = "1=1";
        }

        // Get chat messages
        $currentPage = request("page",1);
        $skip        = ($currentPage - 1) * $limit;

        $loadType       = $request->get('load_type');
        $onlyBroadcast  = false;

        //  if loadtype is brodcast then get the images only
        if($loadType == "broadcast") {
           $onlyBroadcast   = true;
           $loadType        = "images";
        }

        $chatMessages = $object->whatsappAll($onlyBroadcast)->whereRaw($rawWhere);

        if(!$onlyBroadcast){
           $chatMessages = $chatMessages->where('status', '!=', 10);
        }

        $chatMessages =  $chatMessages->skip($skip)->take($limit);
        switch ($loadType) {
            case 'text':
                $chatMessages = $chatMessages->whereNotNull("message")
                                             ->whereNull("media_url")
                                             ->whereRaw('id not in (select mediable_id from mediables WHERE mediable_type LIKE "App%ChatMessage")');
                break;
            case 'images':
                $chatMessages = $chatMessages->whereRaw("(media_url is not null or id in (
                    select
                        mediable_id
                    from
                        mediables
                        join media on id = media_id and extension != 'pdf'
                    WHERE
                        mediable_type LIKE 'App%ChatMessage'
                ) )");
                break;
            case 'pdf':
                $chatMessages = $chatMessages->whereRaw("(id in (
                    select
                        mediable_id
                    from
                        mediables
                        join media on id = media_id and extension = 'pdf'
                    WHERE
                        mediable_type LIKE 'App%ChatMessage'
                ) )");
                break;
            case 'text_with_incoming_img':
                    $chatMessages = $chatMessages->where(function($query) use ($object) {
                    $query->whereRaw("(chat_messages.number = ".$object->phone." and ( media_url is not null 
                                                or id in (
                                                select
                                                    mediable_id
                                                from
                                                    mediables
                                                    join media on id = media_id and extension != 'pdf'
                                                WHERE
                                                    mediable_type LIKE 'App%ChatMessage'
                                            )) )")->orWhere(function($query) {
                                                $query->whereNotNull("message")
                                                ->whereNull("media_url")
                                                ->whereRaw('id not in (select mediable_id from mediables WHERE mediable_type LIKE "App%ChatMessage")');
                                            });
                    });                    
                break;
        }
        $chatMessages = $chatMessages->get();
        // Set empty array with messages
        $messages = [];
        // dd($request->object);
        // Loop over ChatMessages
        foreach ($chatMessages as $chatMessage) {

            $objectname = null;
            if($request->object == 'customer' || $request->object == 'user' || $request->object == 'vendor' || $request->object == 'supplier' || $request->object == 'site_development' || $request->object == 'social_strategy' || $request->object == 'content_management') {
                $objectname = $object->name;
            }
            if($request->object == 'task' || $request->object == 'developer_task') {
                $u = User::find($chatMessage->user_id);
                if($u) {
                    $objectname = $u->name; 
                }
            }
            // Create empty media array  

            $media = [];
            $mediaWithDetails = [];
            $productId = null;
            $parentMedia = [];
            $parentMediaWithDetails = [];
            $parentProductId = null;

            // Check for media
            if ($loadAttached == 1 && $chatMessage->hasMedia(config('constants.media_tags'))) {
                foreach ($chatMessage->getMedia(config('constants.media_tags')) as $key => $image) {
                    // Supplier checkbox
                    if (in_array($request->object, ["supplier"])) {
                        $tempImage = [
                            'key' => $image->getKey(),
                            'image' => $image->getUrl(),
                            'product_id' => '',
                            'special_price' => '',
                            'size' => ''
                        ];

                        $imageKey = $image->getKey();
                        $mediableType = "Product";

                        $productImage = \App\Product::with('Media')
                            ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $imageKey AND mediables.mediable_type LIKE '%$mediableType%')")
                            ->select(['id', 'price_inr_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

                        if ($productImage) {
                            $tempImage[ 'product_id' ] = $productImage->id;
                            $tempImage[ 'special_price' ] = $productImage->price_inr_special;
                            $tempImage[ 'supplier_initials' ] = $this->getSupplierIntials($productImage->supplier);
                            $tempImage[ 'size' ] = $this->getSize($productImage);
                        }

                        $mediaWithDetails[] = $tempImage;
                    } else {
                        // Check for product
                        if (isset($image->id)) {
                            $product = DB::table('mediables')->where('mediable_type', 'App\Product')->where('media_id', $image->id)->get(['mediable_id'])->first();

                            if ($product != null) {
                                $productId = $product->mediable_id;
                            } else {
                                $productId = null;
                            }
                        }

                        // Get media URL
                        $media[] = [
                            'key' => $image->getKey(),
                            'image' => $image->getUrl(),
                            'product_id' => $productId
                        ];
                    }

                }
            }
            if($request->object == 'customer'){

                if(session()->has('encrpyt')){
                   $public = PublicKey::first();
                    if($public != null){
                        $privateKey = hex2bin(session()->get('encrpyt.private'));
                        $publicKey = hex2bin($public->key);
                        $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($privateKey, $publicKey);
                        $message = hex2bin($chatMessage->message);
                        $textMessage = sodium_crypto_box_seal_open($message, $keypair);
                    }
                }else{
                    $textMessage = htmlentities($chatMessage->message);
                }
            }else{
                $textMessage = htmlentities($chatMessage->message);
            }
            //dd($object);
            $isOut = ($chatMessage->number != $object->phone) ? true : false;
            //check for parent message
            $textParent = null;
            if($chatMessage->quoted_message_id) {
                $parentMessage = ChatMessage::find($chatMessage->quoted_message_id);
                if($parentMessage) {
                    if($request->object == 'customer'){
                        if(session()->has('encrpyt')){
                           $public = PublicKey::first();
                            if($public != null){
                                $privateKey = hex2bin(session()->get('encrpyt.private'));
                                $publicKey = hex2bin($public->key);
                                $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($privateKey, $publicKey);
                                $message = hex2bin($parentMessage->message);
                                $textParent = sodium_crypto_box_seal_open($message, $keypair);
                            }
                        }else{
                            $textParent = htmlentities($parentMessage->message);
                        }
                    }else{
                        $textParent = htmlentities($parentMessage->message);
                    }

                    //parent image start here
                    if ($parentMessage->hasMedia(config('constants.media_tags'))) {
                        // foreach ($parentMessage->getMedia(config('constants.media_tags')) as $key => $image) {
                            $images = $parentMessage->getMedia(config('constants.media_tags'));
                            $image = $images->first();
                            // Supplier checkbox
                            if($image) {
                                if (in_array($request->object, ["supplier"])) {
                                    $tempImage = [
                                        'key' => $image->getKey(),
                                        'image' => $image->getUrl(),
                                        'product_id' => '',
                                        'special_price' => '',
                                        'size' => ''
                                    ];
                                    $imageKey = $image->getKey();
                                    $mediableType = "Product";
            
                                    $productImage = \App\Product::with('Media')
                                        ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $imageKey AND mediables.mediable_type LIKE '%$mediableType%')")
                                        ->select(['id', 'price_inr_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();
            
                                    if ($productImage) {
                                        $tempImage[ 'product_id' ] = $productImage->id;
                                        $tempImage[ 'special_price' ] = $productImage->price_inr_special;
                                        $tempImage[ 'supplier_initials' ] = $this->getSupplierIntials($productImage->supplier);
                                        $tempImage[ 'size' ] = $this->getSize($productImage);
                                    }
            
                                    $parentMediaWithDetails[] = $tempImage;
                                } else {
                                    // Check for product
                                    if (isset($image->id)) {
                                        $product = DB::table('mediables')->where('mediable_type', 'App\Product')->where('media_id', $image->id)->get(['mediable_id'])->first();
            
                                        if ($product != null) {
                                            $parentProductId = $product->mediable_id;
                                        } else {
                                            $parentProductId = null;
                                        }
                                    }
            
                                    // Get media URL
                                    $parentMedia[] = [
                                        'key' => $image->getKey(),
                                        'image' => $image->getUrl(),
                                        'product_id' => $parentProductId
                                    ];
                                }
                            }
        
                        // }
                    }
                    //parent image ends
                }
            }
            $messages[] = [
                'id' => $chatMessage->id,
                'type' => $request->object,
                'inout' => ($isOut) ? 'out' : 'in',
                'sendBy'=> ($isOut) ? 'ERP' : $objectname,
                'sendTo'=> ($isOut) ? $object->name : 'ERP',
                'message' => $textMessage,
                'parentMessage' => $textParent,
                'media_url' => $chatMessage->media_url,
                'datetime' => $chatMessage->created_at,
                'media' => is_array($media) ? $media : null,
                'mediaWithDetails' => is_array($mediaWithDetails) ? $mediaWithDetails : null,
                'product_id' => !empty($productId) ? $productId : null,
                'parentMedia' => is_array($parentMedia) ? $parentMedia : null,
                'parentMediaWithDetails' => is_array($parentMediaWithDetails) ? $parentMediaWithDetails : null,
                'parentProductId' => !empty($parentProductId) ? $parentProductId : null,
                'status' => $chatMessage->status,
                'resent' => $chatMessage->resent,
                'customer_id' => $chatMessage->customer_id,
                'approved' => $chatMessage->approved,
                'error_status' => $chatMessage->error_status,
                'is_queue' => $chatMessage->is_queue,
                'is_reviewed' => $chatMessage->is_reviewed,
                'quoted_message_id' => $chatMessage->quoted_message_id
            ];
        }

        // Return JSON
        return response()->json([
            'messages' => $messages
        ]);

       
    }

    public function getSupplierIntials($string)
    {

        $expr = '/(?<=\s|^)[a-z]/i';
        preg_match_all($expr, $string, $matches);

        return strtoupper(implode('', $matches[ 0 ]));
    }

    public function getSize($productImage)
    {
        $size = null;

        if ($productImage->size != null) {
            $size = $productImage->size;
        } else {
            $size = (string)$productImage->lmeasurement . ', ' . (string)$productImage->hmeasurement . ', ' . (string)$productImage->dmeasurement;
        }

        return $size;

    }

    public function setReviewed($id) {
        $message = ChatMessage::find($id);
        if($message) {
            $message->update(['is_reviewed' => 1]);
            return response()->json([
                'message' => 'Successful'
            ],200);
        }
        return response()->json([
            'message' => 'Error'
        ],500);
    }
}
