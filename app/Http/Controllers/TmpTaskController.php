<?php

namespace App\Http\Controllers;

class TmpTaskController extends Controller
{

    public function importLeads()
    {
        set_time_limit(0);
        $leads = \App\Leads::where("customer_id", ">", 0)->get();

        if (!$leads->isEmpty()) {
            foreach ($leads as $lead) {
                try {
                    $jsonBrand = json_decode($lead->multi_brand, true);
                    $jsonCategory = json_decode($lead->multi_category, true);

                    $jsonBrand      =  !empty($jsonBrand) ? (is_array($jsonBrand) ? array_filter($jsonBrand) : [$jsonBrand]) : []; 
                    $jsonCategory   =  !empty($jsonCategory) ? (is_array($jsonCategory) ? array_filter($jsonCategory) : [$jsonCategory]) : [];

                    if ($lead->selected_product) {
                        $selectedProduct = json_decode($lead->selected_product, true);

                        $product = \App\Product::where("id", (is_array($selectedProduct) ? $selectedProduct[0] : $selectedProduct))->first();

                        if ($product) {
                            if (empty($jsonBrand)) {
                                $jsonBrand = [$product->brand];
                            }

                            if (empty($jsonCategory)) {
                                $jsonCategory = [$product->category];
                            }
                        }

                    }

                    $brandSegment = null;
                    if (!empty($jsonBrand)) {
                        $brand = \App\Brand::whereIn("id",$jsonBrand)->get();
                        if ($brand) {
                            $brandSegmentArr = [];
                            foreach ($brand as $v) {
                                $brandSegmentArr[] = $v->brand_segment;
                            }
                            $brandSegment = implode(",", array_unique($brandSegmentArr));
                        }
                    }

                    $erpLead = \App\ErpLeads::where([
                        'brand_id' => isset($jsonBrand[ 0 ]) ? $jsonBrand[ 0 ] : '',
                        'category_id' => isset($jsonCategory[ 0 ]) ? $jsonCategory[ 0 ] : '',
                        'customer_id' => $lead->customer_id,
                        'brand_segment' => $brandSegment,
                    ])->first();

                    if (!$erpLead) {
                        $erpLead = new \App\ErpLeads;
                    }
                    
                    $erpLead->lead_status_id = $lead->status;
                    $erpLead->customer_id = $lead->customer_id;
                    $erpLead->product_id = !empty($product) ? $product->id : null;
                    $erpLead->brand_id = isset($jsonBrand[ 0 ]) ? $jsonBrand[ 0 ] : null;
                    $erpLead->brand_segment = $brandSegment;
                    $erpLead->category_id = isset($jsonCategory[ 0 ]) ? $jsonCategory[ 0 ] : null;
                    $erpLead->color = null;
                    $erpLead->size = $lead->size;
                    $erpLead->min_price = 0.00;
                    $erpLead->max_price = 0.00;
                    $erpLead->created_at = $lead->created_at;
                    $erpLead->updated_at = $lead->updated_at;
                    $erpLead->save();

                    $mediaArr = $lead->getMedia(config('constants.media_tags'));
                    foreach ($mediaArr as $media) {
                        \DB::table('mediables')->where('media_id', $media->id)->where('mediable_type', 'App\ErpLeads')->delete();
                        $erpLead->attachMedia($media, config('constants.media_tags'));
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // do what you want here with $e->getMessage();
                }
            }
        }

    }

}
