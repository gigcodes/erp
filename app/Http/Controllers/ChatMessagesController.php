<?php

namespace App\Http\Controllers;

use App\Customer;
use App\User;
use App\Vendor;
use App\Supplier;
use Illuminate\Http\Request;

class ChatMessagesController extends Controller
{
    /**
     * Load more messages from chat_messages
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadMoreMessages(Request $request)
    {
        // Set limit of messages
        $limit = $request->get("limit", 3);
        $loadAttached = $request->get("load_attached", 0);

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
            case 'supplier':
                $object = Supplier::find($request->object_id);
                break;    
            default:
                $object = Customer::find($request->object);
        }

        // Get chat messages
        $chatMessages = $object->whatsappAll()->whereRaw("(message!='' or media_url!='')")->skip(0)->take($limit)->get();

        // Set empty array with messages
        $messages = [];

        // Loop over ChatMessages
        foreach ($chatMessages as $chatMessage) {
            // Create empty media array
            $media = [];
            $mediaWithDetails = [];

            // Check for media
            if ($loadAttached == 1 && $chatMessage->hasMedia(config('constants.media_tags'))) {
                foreach ($chatMessage->getMedia(config('constants.media_tags')) as $key => $image) {
                    
                    if(in_array($request->object,["supplier"])) {

                        $temp_image = [
                            'key' => $image->getKey(),
                            'image' => $image->getUrl(),
                            'product_id' => '',
                            'special_price' => '',
                            'size' => ''
                        ];

                        $image_key = $image->getKey();
                        $mediable_type = "Product";

                        $product_image =\App\Product::with('Media')
                        ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
                        ->select(['id', 'price_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

                        if ($product_image) {
                            $temp_image[ 'product_id' ] = $product_image->id;
                            $temp_image[ 'special_price' ] = $product_image->price_special;
                            $temp_image[ 'supplier_initials' ] = $this->getSupplierIntials($product_image->supplier);
                            $temp_image[ 'size' ] = $this->getSize($product_image);
                        }

                        $mediaWithDetails[] = $temp_image;
                    }else{
                        $media[] = $image->getUrl();
                    }

                }
            }

            $messages[] = [
                'type'  => $request->object,
                'inout' => $chatMessage->number != $object->phone ? 'out' : 'in',
                'message' => $chatMessage->message,
                'media_url' => $chatMessage->media_url,
                'datetime' => $chatMessage->created_at,
                'media' => is_array($media) ? $media : null,
                'mediaWithDetails' => is_array($mediaWithDetails) ? $mediaWithDetails : null
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
}
