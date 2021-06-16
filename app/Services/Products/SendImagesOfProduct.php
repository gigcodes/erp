<?php

namespace App\Services\Products;

use Illuminate\Http\Request;

class SendImagesOfProduct
{
    public function check($chatMessage)
    {
        if (!empty($chatMessage->message)) {
            $sentence = preg_replace('/\s+/', ' ', $chatMessage->message);
            $sentence = explode(" ", $sentence);

            $brand    = $this->checkWithBrand($sentence);
            $category = $this->checkWithCategory($sentence);

            \Log::info(print_r(["Started check for the sentance",$sentence,($brand) ? $brand->id : 0 , ($category) ? $category->id : 0],true));

            if($brand && $category) {

                $myRequest = new Request();
                $myRequest->setMethod('POST');
                $myRequest->request->add(['brand' => [$brand->id]]);
                $myRequest->request->add(['category' => [$category->id]]);
                $myRequest->request->add(['submit_type' => 'send-to-approval']);
                $myRequest->request->add(['limit' => 10]);
                $myRequest->request->add(['need_to_send_message' => 1]);
                $myRequest->request->add(['keyword_matched' => $chatMessage->message]);


                (new \App\Http\Controllers\ProductController)->attachImages('customer',$chatMessage->customer_id,null,null,$myRequest);
            }

        }
    }

    public function checkWithBrand($sentence)
    {
        foreach ((array) $sentence as $s) {
            $brand = \App\Brand::where("name", "like", $s)->orderBy("id","asc")->first();
            if ($brand) {
                return $brand;
            }
        }

        return null;
    }

    public function checkWithCategory($sentence)
    {
        foreach ((array) $sentence as $s) {
            $category = \App\Category::where("title", "like", $s)->first();
            if ($category) {
                return $category;
            }
        }
    }

}
