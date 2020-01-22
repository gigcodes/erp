<?php

namespace App\Http\Controllers\Marketing;

use App\Customer;
use App\CustomerMarketingPlatform;
use App\Mailinglist;
use App\MailingRemark;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class MailinglistController extends Controller
{
    /**
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $services = Service::all();
        $list = Mailinglist::paginate(15);

        return view('marketing.mailinglist.index', compact('services', 'list'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $curl = curl_init();
        $data = [
            "folderId" => 1,
            "name" => $request->name
        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/lists",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);
        Mailinglist::create([
            'id' => $res->id,
            'name' => $request->name,
            'service_id' => $request->service_id,
            'remote_id' => $res->id,
        ]);

        return response()->json(true);
    }

    /**
     * @param $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id, Request $request)
    {
        $customers = Customer::whereNotNull('email');
        if(!is_null($request->term)){
            $customers = $customers->where('email', 'LIKE', "%{$request->term}%");
        }
        //Total Result
        if (request('total') != null){

            //search with date
            if(request('total') == 1 && request('customrange') != null){
                $range = explode(' - ', request('customrange'));
                if($range[0] == end($range)){
                    $customers->whereHas('customerMarketingPlatformActive', function ($qu) use ($range) {
                        $qu->whereDate('created_at', end($range))->where('active', 1);
                    })->where('do_not_disturb',0);
                }else{
                    $customers->whereHas('customerMarketingPlatformActive', function ($qu) use ($range) {
                        $qu->whereBetween('created_at', [$range[0], end($range)])->where('active', 1);
                    })->where('do_not_disturb',0);
                }
            }

            elseif(request('total') == 1){
                $customers->whereHas('customerMarketingPlatformActive', function ($qu) use ($request) {
                    $qu->where('active', 1);
                })->where('do_not_disturb',0);
            }

            if(request('total') == 2 && request('customrange') != null){
                $range = explode(' - ', request('customrange'));
                if($range[0] == end($range)){
                    $customers->doesntHave('customerMarketingPlatformActive')->whereDate('created_at',end($range))->where('do_not_disturb',0);
                }else{
                    $customers->doesntHave('customerMarketingPlatformActive')->whereBetween('created_at', [$range[0], end($range)])->where('do_not_disturb',0);
                }
            }

            if(request('total') == 2){
                $customers->doesntHave('customerMarketingPlatformActive')->where('do_not_disturb',0);
            }

            if(request('total') == 3 && request('customrange') != null){
                $range = explode(' - ', request('customrange'));
                if($range[0] == end($range)){
                    $customers->where('do_not_disturb', 1)->whereDate('updated_at',end($range));
                }else{
                    $customers->where('do_not_disturb', 1)->whereBetween('updated_at', [$range[0], end($range)]);
                }
            }

            elseif(request('total') == 3){
                $customers->where('do_not_disturb', 1);
            }

            if(request('total') == 4 && request('customrange') != null){
                $range = explode(' - ', request('customrange'));
                if($range[0] == end($range)){

                    $customers->whereHas('leads', function ($qu) use ($range) {
                        $qu->whereDate('created_at', end($range));
                    });

                }else{
                    $customers->whereHas('leads', function ($qu) use ($range) {
                        $qu->whereBetween('created_at', [$range[0], end($range)]);
                    });
                }
            }

            elseif(request('total') == 4){
                $customers->whereHas('leads');
            }

            if(request('total') == 5 && request('customrange') != null){
                $range = explode(' - ', request('customrange'));
                if($range[0] == end($range)){

                    $customers->whereHas('orders', function ($qu) use ($range) {
                        $qu->whereDate('created_at', end($range));
                    });

                }else{
                    $customers->whereHas('orders', function ($qu) use ($range) {
                        $qu->whereBetween('created_at', [$range[0], end($range)]);
                    });
                }
            }

            elseif(request('total') == 5){
                $customers->whereHas('orders');
            }

            if(request('total') == 6 && request('customrange') != null){
                $range = explode(' - ', request('customrange'));
                if($range[0] == end($range)){

                    $customers->whereHas('customerMarketingPlatformActive', function ($qu) use ($range) {
                        $qu->where('active', 1);
                    })->where('broadcast_number',null)->whereDate('created_at', end($range));;

                }else{
                    $customers->whereHas('customerMarketingPlatformActive', function ($qu) use ($range) {
                        $qu->where('active', 1);
                    })->where('broadcast_number',null)->whereBetween('created_at', [$range[0], end($range)]);
                }
            }

            elseif(request('total') == 6){
                $customers->whereHas('customerMarketingPlatformActive', function ($qu) use ($request) {
                    $qu->where('active', 1);
                })->where('broadcast_number',null)->where('do_not_disturb',0);
            }

            if(request('total') == 7 && request('customrange') != null){
                $range = explode(' - ', request('customrange'));
                if($range[0] == end($range)){
                    $customers->whereHas('notDelieveredImQueueMessage', function ($qu) use ($range) {
                        $qu->whereDate('send_after', end($range));
                    });
                }else{
                    $customers->whereHas('notDelieveredImQueueMessage', function ($qu) use ($range) {
                        $qu->whereBetween('send_after', [$range[0], end($range)]);
                    });
                }
            }

            elseif(request('total') == 7){
                $customers->whereHas('notDelieveredImQueueMessage');
            }

        }
        $customers = $customers->select('email', 'id', 'name', 'do_not_disturb')->paginate(20);
        $list = Mailinglist::where('remote_id', $id)->with('listCustomers')->first();

        $contacts = $list->listCustomers->pluck('id')->toArray();

        $countDNDCustomers = Customer::where('do_not_disturb','1')->count();

        return view('marketing.mailinglist.show', compact('customers', 'id', 'contacts', 'list', 'countDNDCustomers'));
    }

    /**
     * @param $id
     * @param $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToList($id, $email)
    {
        $curl = curl_init();
        $data = [
            "email" => $email,
            "listIds" => [intval($id)]
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);

        if (isset($res->message)) {
            if($res->message == 'Contact already exist'){
                $curl3 = curl_init();
                curl_setopt_array($curl3, array(
                    CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/".$email,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "DELETE",
                    CURLOPT_HTTPHEADER => array(
                        "api-key: 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",
                        "Content-Type: application/json"
                    ),
                ));
                $respw = curl_exec($curl3);
                curl_close($curl3);
                $respw = json_decode($respw);

                $curl2 = curl_init();
                curl_setopt_array($curl2, array(
                    CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => array(
                        "api-key: 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",
                        "Content-Type: application/json"
                    ),
                ));
                $resp = curl_exec($curl2);
                curl_close($curl2);
                $ress = json_decode($resp);
                if(isset($ress->message)){
                    return response()->json(['status' => 'error']);
                }
                $customer = Customer::where('email', $email)->first();
                $mailinglist = Mailinglist::find($id);
                \DB::table('list_contacts')->where('customer_id', $customer->id)->delete();
                $mailinglist->listCustomers()->attach($customer->id);

                return response()->json(['status' => 'success']);
            }
        } else {
            $customer = Customer::where('email', $email)->first();
            $mailinglist = Mailinglist::find($id);
            $mailinglist->listCustomers()->attach($customer->id);

            return response()->json(['status' => 'success']);
        }
    }

    /**
     * @param $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, $email)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/".$email,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);

        if (isset($res->message)) {
            return response()->json(['status' => 'error']);
        } else {
            $customer = Customer::where('email', $email)->first();
            $mailinglist = Mailinglist::find($id);
            $mailinglist->listCustomers()->detach($customer->id);

            return response()->json(['status' => 'success']);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteList($id)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/lists/".$id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);

        if (isset($res->message)) {
            return redirect()->back()->withErrors($res->message);
        } else {
            Mailinglist::where('remote_id', $id)->delete();
            return redirect()->back()->with('message', 'Removed successfully.');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRemark(Request $request)
    {
        $remark = $request->input('remark');
        $id = $request->input('id');
        MailingRemark::create([
            'customer_id' => $id,
            'text' => $remark,
            'user_name' => \Auth::user()->name,
            'user_id' => \Auth::user()->id,
        ]);
        return response()->json(['remark' => $remark], 200);
    }

    public function getBroadCastRemark(Request $request)
    {
        $id = $request->input('id');

        $remark = MailingRemark::where('customer_id', $id)->whereNotNull('text')->get();

        return response()->json($remark, 200);
    }

    public function addManual(Request $request)
    {
        $email = $request->email;
        $id = $request->id;
        $curl = curl_init();
        $data = [
            "email" => $email,
            "listIds" => [intval($id)]
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);

        if (isset($res->message)) {
            if($res->message == 'Contact already exist'){
                $curl3 = curl_init();
                curl_setopt_array($curl3, array(
                    CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/".$email,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "DELETE",
                    CURLOPT_HTTPHEADER => array(
                        "api-key: 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",
                        "Content-Type: application/json"
                    ),
                ));
                $respw = curl_exec($curl3);
                curl_close($curl3);
                $respw = json_decode($respw);

                $curl2 = curl_init();
                curl_setopt_array($curl2, array(
                    CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => array(
                        "api-key: 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",
                        "Content-Type: application/json"
                    ),
                ));
                $resp = curl_exec($curl2);
                curl_close($curl2);
                $ress = json_decode($resp);
                if(isset($ress->message)){
                    return response()->json(['status' => 'error']);
                }
                $customer = Customer::where('email', $email)->first();
                $mailinglist = Mailinglist::find($id);
                \DB::table('list_contacts')->where('customer_id', $customer->id)->delete();
                $mailinglist->listCustomers()->attach($customer->id);

                return response()->json(['status' => 'success']);
            }
        } else {
            $customer = Customer::where('email', $email)->first();
            $mailinglist = Mailinglist::find($id);
            $mailinglist->listCustomers()->attach($customer->id);

            return response()->json(['status' => 'success']);
        }
    }
}
