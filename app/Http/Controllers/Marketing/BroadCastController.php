<?php

namespace App\Http\Controllers\Marketing;

use App\Customer;
use App\Http\Controllers\Controller;
use App\CustomerMarketingPlatform;
use Illuminate\Http\Request;
use App\Setting;
use Auth;
use Validator;
use Response;
use App\ApiKey;

class BroadCastController extends Controller
{
	public function index(Request $request)
	{
		 if($request->term || $request->date || $request->number || $request->broadcast || $request->manual || $request->remark ){

        $query =  Customer::query();

            //global search term
        if (request('term') != null) {
            $query->where('whatsapp_number', 'LIKE', "%{$request->term}%")
                    ->orWhereHas('broadcastLatest', function ($qu) use ($request) {
                      $qu->where('id', 'LIKE', "%{$request->term}%");
                      })
                    ->orWhereHas('remark', function ($qu) use ($request) {
                      $qu->where('remark', 'LIKE', "%{$request->term}%");
                      });
            }

        if (request('date') != null) {
            $query->whereDate('created_at', request('date'));
        }

               //if number is not null 
        if (request('number') != null) {
            $query->where('whatsapp_number','LIKE', '%' . request('number') . '%');
        }

        if (request('broadcast') != null) {
                $query->whereHas('broadcastLatest', function ($qu) use ($request) {
                    $qu->where('id', 'LIKE', '%' . request('broadcast') . '%');
                    });
            }

        if (request('manual') != null) {
                $query->whereHas('manual', function ($qu) use ($request) {
                    $qu->where('active', request('manual'));
                    });
            }    
        
        if (request('remark') != null) {
                $query->whereHas('remark', function ($qu) use ($request) {
                    $qu->where('remark', 'LIKE', '%' . request('remark') . '%');
                    });
            }      

        $customers = $query->orderby('id','desc')->where('do_not_disturb',0)->paginate(Setting::get('pagination')); 

        }else{
          $customers = Customer::select('id','whatsapp_number')->orderby('id','desc')->where('do_not_disturb',0)->paginate(Setting::get('pagination'));   
        }
        $apiKeys = ApiKey::all();
        if ($request->ajax()) {
        return response()->json([
            'tbody' => view('marketing.broadcasts.partials.data', compact('customers','apiKeys'))->render(),
            'links' => (string)$customers->render()
        ], 200);
    }

	return view('marketing.broadcasts.index', [
      'customers' => $customers,
      'apiKeys' => $apiKeys,
    ]);

	}

	public function addToDND(Request $request)
	{
		
       $id = $request->id;
       $customer = Customer::findOrFail($id);
       $customer->do_not_disturb = $request->type;
       $customer->update(); 
       return response()->json([
            'status' => 'success'
        ]);
	}

	public function getBroadCastRemark(Request $request)
    {
        $id = $request->input('id');

        $remark = CustomerMarketingPlatform::where('customer_id', $id)->whereNotNull('remark')->get();

        return response()->json($remark, 200);
    }

    public function addRemark(Request $request)
    {
        $remark = $request->input('remark');
        $id = $request->input('id');
       	$remark_entry = CustomerMarketingPlatform::create([
                'customer_id' => $id,
                'remark' => $remark,
                'marketing_platform_id' => '1',
                'user_name' => $request->user_name ? $request->user_name : Auth::user()->name
            ]);
        

        return response()->json(['remark' => $remark], 200);

    }

    public function addManual(Request $request)
	{
	   $id = $request->id;
       
       $remark = CustomerMarketingPlatform::where('customer_id',$id)->whereNull('remark')->first();

        if($remark == null){
            $remark_entry = CustomerMarketingPlatform::create([
                'customer_id' => $id,
                'marketing_platform_id' => '1',
                'active' => 1,
                'user_name' => $request->user_name ? $request->user_name : Auth::user()->name
            ]);

        }else{
           $remark->active = $request->type;
            $remark->update();
        }

       return response()->json([
            'status' => 'success'
        ]);

       
	}

    public function updateWhatsAppNumber(Request $request)
    {
        $id = $request->id;
        $number = $request->number;

        $customer = Customer::findOrFail($id);
        $customer->whatsapp_number = $number;
        $customer->update();

        return response()->json([
            'status' => 'success'
        ]);
    }
}