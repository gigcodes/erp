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
                    $erpLead = new \App\ErpLeads;
                    $erpLead->lead_status_id = $lead->status;
                    $erpLead->customer_id = $lead->customer_id;
                    $erpLead->product_id = null;

                    $jsonBrand = json_decode($lead->multi_brand, true);
                    $jsonCategory = json_decode($lead->multi_category, true);

                    $erpLead->brand_id = isset($jsonBrand[ 0 ]) ? $jsonBrand[ 0 ] : null;
                    $erpLead->category_id = isset($jsonCategory[ 0 ]) ? $jsonCategory[ 0 ] : null;
                    $erpLead->color = null;
                    $erpLead->size = $lead->size;
                    $erpLead->min_price = 0.00;
                    $erpLead->max_price = 0.00;
                    $erpLead->created_at = $lead->created_at;
                    $erpLead->updated_at = $lead->updated_at;
                    $erpLead->save();
                } catch (\Illuminate\Database\QueryException $e) {
                    // do what you want here with $e->getMessage();
                }
            }
        }

    }

}
