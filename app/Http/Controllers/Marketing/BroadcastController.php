<?php

namespace App\Http\Controllers\Marketing;

use App\Customer;
use App\Helpers\InstantMessagingHelper;
use App\Http\Controllers\Controller;
use App\CustomerMarketingPlatform;
use Illuminate\Http\Request;
use App\Setting;
use Auth;
use Validator;
use Response;
use App\Order;
use App\ApiKey;
use App\ErpLeads;
use App\Marketing\WhatsappConfig;

class BroadcastController extends Controller
{
    /**
     * Getting BroadCast Page with Ajax Search.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Customer  id , term , date , number , broadcast , manual , remark , name
     * @return \Illuminate\Http\View And Ajax
     */
    public function index(Request $request)
    {
        if ($request->term || $request->date || $request->number || $request->broadcast || $request->manual || $request->remark || $request->name) {

            $query = Customer::query();

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
                $query->where('whatsapp_number', 'LIKE', '%' . request('number') . '%');
            }
            //if number is not null
            if (request('name') != null) {
                $query->where('name', 'LIKE', '%' . request('name') . '%');
            }

            if (request('broadcast') != null) {
                $query->whereHas('broadcastLatest', function ($qu) use ($request) {
                    $qu->where('group_id', 'LIKE', '%' . request('broadcast') . '%');
                });
            }

            if (request('manual') != null) {
                $query->whereHas('customerMarketingPlatformActive', function ($qu) use ($request) {
                    $qu->where('active', request('manual'));
                });
            }

            if (request('remark') != null) {
                $query->whereHas('customerMarketingPlatformRemark', function ($qu) use ($request) {
                    $qu->where('remark', 'LIKE', '%' . request('remark') . '%');
                });
            }

            $customers = $query->orderby('id', 'desc')->where('do_not_disturb', 0)->paginate(Setting::get('pagination'));

        } else {
            //Order List
            $orders = Order::select('customer_id')->whereNotNull('customer_id')->get();
            foreach ($orders as $order) {
                $orderArray[] = $order->customer_id;
            }
            $orderList = implode(",", $orderArray);

            //Leads List
            $leads = ErpLeads::select('customer_id')->whereNotNull('customer_id')->get();
            foreach ($leads as $lead) {
                $leadArray[] = $lead->customer_id;
            }
            $leadList = implode(",", $leadArray);
            

            $customers = Customer::select('name',\DB::raw('IF(id IN ('.$orderList.') , 1 , 0) AS priority_order , IF(id IN ('.$orderList.') , 1 , 0) AS priority_lead'))->orderby('priority_order','desc')->orderby('priority_lead','desc')->paginate(Setting::get('pagination'));

        }
        $numbers = WhatsappConfig::where('is_customer_support', 0)->get();
        $apiKeys = ApiKey::all();
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('marketing.broadcasts.partials.data', compact('customers', 'apiKeys', 'numbers'))->render(),
                'links' => (string)$customers->render()
            ], 200);
        }

        return view('marketing.broadcasts.index', [
            'customers' => $customers,
            'apiKeys' => $apiKeys,
            'numbers' => $numbers,
        ]);

    }

    /**
     * Update Customer TO DND .
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Customer  id is $request->id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Getting Remark From CustomerMarketingPlatform table.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\CustomerMarketingPlatform  customer_id = $request->id
     * @return \Illuminate\Http\Response
     */

    public function getBroadCastRemark(Request $request)
    {
        $id = $request->input('id');

        $remark = CustomerMarketingPlatform::where('customer_id', $id)->whereNotNull('remark')->get();

        return response()->json($remark, 200);
    }

    /**
     * Adding Remark to CustomerMarketingPlatform table.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\CustomerMarketingPlatform  id and remark
     * @return \Illuminate\Http\Response
     */
    public function addRemark(Request $request)
    {

        $remark = $request->input('remark');
        $id = $request->input('id');
        CustomerMarketingPlatform::create([
            'customer_id' => $id,
            'remark' => $remark,
            'marketing_platform_id' => '1',
            'user_name' => Auth::user()->name,
        ]);
        return response()->json(['remark' => $remark], 200);

    }

    /**
     * Adding Customer to CustomerMarketingPlatform table.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Customer $request ->id
     * @return \Illuminate\Http\Response
     */
    public function addManual(Request $request)
    {
        // Set customer ID and try to find customer
        $customerId = $request->id;
        $customer = Customer::findOrFail($customerId);

        // Do we have a customer?
        if ($customer != null && $request->type == 1) {
            // Set welcome message
            $welcomeMessage = InstantMessagingHelper::replaceTags($customer, Setting::get('welcome_message'));

            // Set empty number with count
            $numberWithCount = [];

            // Get Whatsapp number with lowest customer count
            $whatsappConfigs = WhatsappConfig::where('is_customer_support', 0)->get();

            // Check if we have results
            if ($whatsappConfigs != null && count($whatsappConfigs) > 0) {
                // Set temp minimum value
                $tmpMinValue = 1000000;

                // Set number with least customers
                $numberWithLeastCustomers = null;

                // Loop over numbers
                foreach ($whatsappConfigs as $whatsappConfig) {
                    // Check if number is already set
                    if ($customer->whatsapp_number == $whatsappConfig->number) {
                        $numberWithLeastCustomers = $customer->whatsapp_number;
                        break;
                    }

                    // Check for lower count
                    if ($whatsappConfig->customer->count() < $tmpMinValue) {
                        // Set new tmp minimum value
                        $tmpMinValue = $whatsappConfig->customer->count();

                        // Set new number with least customers
                        $numberWithLeastCustomers = $whatsappConfig->number;
                    }
                }

                // Update customer with new number
//                $customer->whatsapp_number = $numberWithLeastCustomers;
//                $customer->update();

                // Send the welcome message
                InstantMessagingHelper::scheduleMessage($customer->phone, $numberWithLeastCustomers, $welcomeMessage);
            }
        }

        //Add Customer to Customer Marketing Table
        $remark = CustomerMarketingPlatform::where('customer_id', $customerId)->whereNull('remark')->first();
        if ($remark == null) {
            CustomerMarketingPlatform::create([
                'customer_id' => $customerId,
                'marketing_platform_id' => '1', // WhatsApp
                'active' => 1,
                'user_name' => Auth::user()->name,
            ]);

        } else {
            $remark->active = $request->type;
            $remark->update();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Update the customer number.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Customer $request ->id
     * @return \Illuminate\Http\Response
     */
    public function updateWhatsAppNumber(Request $request)
    {
        //Updating Customer WhatsAppNumber
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