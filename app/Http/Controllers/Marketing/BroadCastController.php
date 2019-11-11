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
use App\Marketing\WhatsAppConfig;

class BroadCastController extends Controller
{
	public function index(Request $request)
	{
		 if($request->term || $request->date || $request->number || $request->broadcast || $request->manual || $request->remark || $request->name){

        $query =  Customer::query();

            //global search term
        if (request('term') != null) {
            $query->where('whatsapp_number', 'LIKE', "%{$request->term}%")
                    ->orWhere('name', 'LIKE', "%{$request->term}%")
                    ->orWhereHas('broadcastLatest', function ($qu) use ($request) {
                      $qu->where('group_id', 'LIKE', "%{$request->term}%");
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
                //if number is not null 
        if (request('name') != null) {
            $query->where('name','LIKE', '%' . request('name') . '%');
        }

        if (request('broadcast') != null) {
                $query->whereHas('broadcastLatest', function ($qu) use ($request) {
                    $qu->where('group_id', 'LIKE', '%' . request('broadcast') . '%');
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
          $customers = Customer::select('id','name','whatsapp_number')->orderby('id','desc')->where('do_not_disturb',0)->paginate(Setting::get('pagination'));   
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
                'user_name' => Auth::user()->name,
            ]);
        

        return response()->json(['remark' => $remark], 200);

    }

    public function addManual(Request $request)
	{
	   $id = $request->id;
       $customer = Customer::findOrFail($id);
       if($customer != null && $request->type == 1){
        
        if(count($customer->orders) == 0 && count($customer->leads) == 0){
            
            $welcome_message = Setting::get('welcome_message');
            $customer->phone = '+918082488108';
            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($customer->phone, '',$welcome_message, '', '','',$id);

        }

        $number_with_count = array();
        $whatsapps = WhatsAppConfig::where('is_customer_support',0)->get();
            if(count($whatsapps) != null){
            foreach ($whatsapps as $whatsapp) {
               $count = count($whatsapp->customer);
               $number = $whatsapp->number;
               array_push($number_with_count,['number' => $number , 'count' => $count]);

            }
            
            $temp=$number_with_count[0]['count'];
            $number = 0;
            foreach($number_with_count as $key => $values)
            {
                if($values['count']<=$temp)
                {
                    $temp=$values['count'];
                    $number=$values['number']; 
                }
            }
            $customer->whatsapp_number = $number;
            $customer->update();
            }   
        }
        $remark = CustomerMarketingPlatform::where('customer_id',$id)->whereNull('remark')->first();
        if($remark == null){
            $remark_entry = CustomerMarketingPlatform::create([
                'customer_id' => $id,
                'marketing_platform_id' => '1',
                'active' => 1,
                'user_name' => Auth::user()->name,
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