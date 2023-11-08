<?php

namespace App\Http\Controllers;

use App\VirtualminDomain;
use App\VirtualminHelper;
use Illuminate\Http\Request;
use App\Models\VirtualminDomainHistory;
use App\Models\VirtualminDomainDnsRecords;
use App\Models\VirtualminDomainDnsLogs;
use App\Models\VirtualminDomainLogs;
use Auth;

class VirtualminDomainController extends Controller
{
    private $virtualminHelper;

    public function __construct(VirtualminHelper $virtualminHelper)
    {
        $this->virtualminHelper = $virtualminHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $status = $request->get('status');

        // data search action
        $domains = VirtualminDomain::latest();

        if (! empty($keyword) || isset($keyword)) {
            $domains = $domains->where('name', 'LIKE', '%' . $keyword . '%');
        }
        if (! empty($status) || isset($status)) {
            $domains = $domains->where('is_enabled', $status);
        }

        $domains = $domains->paginate(10);

        return view('virtualmin-domain.index', ['domains' => $domains]);
    }

    public function syncDomains()
    {
        $this->virtualminHelper->syncDomains();

        return redirect()->back()->with('success', 'Domains synced successfully.');
    }

    public function domainCreate(Request $request)
    {
        try {

            //$url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
            $url = getenv('CLOUDFLARE_CREATE_DOMAIN_URL');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $account['id'] = getenv('CLOUDFLARE_ACCOUNT_ID');
            $parameters = [
                'name' => $request->name,
                'jump_start' => true,
                'account' => $account,
            ];

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

            $headers = [];
            $headers[] = 'X-Auth-Email: ' . getenv('CLOUDFLARE_EMAIL');
            $headers[] = 'X-Auth-Key: ' . getenv('CLOUDFLARE_AUTH_KEY');
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            \Log::info('Virtualmin Domain API result: ' . $result);
            \Log::info('Virtualmin Domain API Error Number: ' . curl_errno($ch));
            if (curl_errno($ch)) {
                \Log::info('API Error: ' . curl_error($ch));
                //MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Error', 'response' => curl_error($ch)]);
            }
            $response = json_decode($result);

            VirtualminDomainLogs::create(['url' => $url, 'created_by' => Auth::user()->id, 'command' => json_encode($parameters), 'status' => 'Error', 'response' => $result]);

            curl_close($ch);

            if(!empty($response->success)){

                $VirtualminDomain = new VirtualminDomain();
                $VirtualminDomain->name = $request->name;
                $VirtualminDomain->save();

                $virtualminDomainHistory = new VirtualminDomainHistory();
                $virtualminDomainHistory->Virtual_min_domain_id = $VirtualminDomain->id;
                $virtualminDomainHistory->user_id = Auth::user()->id;
                $virtualminDomainHistory->command = json_encode($parameters);
                $virtualminDomainHistory->error = $result['error'] ?? null;
                $virtualminDomainHistory->output = $result;
                $virtualminDomainHistory->status = $response->result->status;
                $virtualminDomainHistory->save();

                return response()->json(['code' => 200, 'message' => 'Domain Create successfully']);

            } else{
                return response()->json(['code' => 500, 'message' => $response->errors[0]->message]);
            }

            
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function adnsCreate(Request $request)
    {
        try {

            $this->validate($request, [
                'Virtual_min_domain_id' => 'required',
                'name' => 'required|alpha',
                'type' => 'required',
            ]);

            $domain = VirtualminDomain::findOrFail($request->Virtual_min_domain_id);

            if(!empty($domain)){

                //$url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
                $url = getenv('CLOUDFLARE_DOMAIN_TOKEN_VERIFY_URL');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $headers = [];
                $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                //$headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $response = json_decode($result);
                curl_close($ch);

                if($response->success==1){
                    $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $headers = [];
                    $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                    //$headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    $response = json_decode($result);
                    curl_close($ch);

                    $zoneIdentifier = '';
                    if(!empty($response->result)){
                        foreach ($response->result as $key => $value) {
                            if($domain->name==$value->name){
                                $zoneIdentifier = $value->id;
                                break;
                            }
                        }
                    }

                    if(!empty($zoneIdentifier)){

                        $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL').'/'.$zoneIdentifier;

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

                        $parameters = [
                            'content' => '49.36.91.203', //$_SERVER['REMOTE_ADDR']
                            'name' => $request->name.'.'.$domain->name,
                            'proxied' => 'boolean',
                            'type' => $request->type,
                        ];

                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

                        $headers = [];
                        $headers[] = 'X-Auth-Email: ' . getenv('CLOUDFLARE_EMAIL');
                        $headers[] = 'X-Auth-Key: ' . getenv('CLOUDFLARE_AUTH_KEY');
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result = curl_exec($ch);

                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        \Log::info('Virtualmin Domain CREATE A DNS API result: ' . $result);
                        \Log::info('Virtualmin Domain CREATE A DNS API Error Number: ' . curl_errno($ch));
                        if (curl_errno($ch)) {
                            \Log::info('API Error: ' . curl_error($ch));
                        }
                        $response = json_decode($result);

                        VirtualminDomainDnsLogs::create(['url' => $url, 'created_by' => Auth::user()->id, 'command' => json_encode($parameters), 'status' => 'Error', 'response' => $result]);

                        curl_close($ch);

                        return $response;

                        if(!empty($response->success)){

                            $VirtualminDomain = new VirtualminDomain();
                            $VirtualminDomain->name = $request->name;
                            $VirtualminDomain->save();

                            $virtualminDomainHistory = new VirtualminDomainHistory();
                            $virtualminDomainHistory->Virtual_min_domain_id = $VirtualminDomain->id;
                            $virtualminDomainHistory->user_id = Auth::user()->id;
                            $virtualminDomainHistory->command = json_encode($parameters);
                            $virtualminDomainHistory->error = $result['error'] ?? null;
                            $virtualminDomainHistory->output = $result;
                            $virtualminDomainHistory->status = $response->result->status;
                            $virtualminDomainHistory->save();

                            return response()->json(['code' => 200, 'message' => 'Domain DNS Create successfully']);

                        } else{
                            return response()->json(['code' => 500, 'message' => $response->errors[0]->message]);
                        }

                    } else {
                        return response()->json(['code' => 500, 'message' => 'Invalid zone identifier']);
                    }
                } else {
                    return response()->json(['code' => 500, 'message' => 'Invalid API Token']);
                }
            } else{
                return response()->json(['code' => 500, 'message' => 'Domain is not exists.']);
            }
            
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function enableDomain(Request $request, $id)
    {
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            // Enable the domain using Virtualmin API
            $response = $this->virtualminHelper->enableDomain($domain);

            // Maintain Log depends on the response in new Table

            // Update the domain status in your local database if needed
            $domain->update(['is_enabled' => true]);

            return redirect()->back()->with('success', 'Domain enabled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function disableDomain(Request $request, $id)
    {
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            // Disable the domain using Virtualmin API
            $response = $this->virtualminHelper->disableDomain($domain);

            // Maintain Log depends on the response in new Table

            // Update the domain status in your local database if needed
            $domain->update(['is_enabled' => false]);

            return redirect()->back()->with('success', 'Domain disabled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function deleteDomain(Request $request, $id)
    {
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            // Disable the domain using Virtualmin API
            $response = $this->virtualminHelper->deleteDomain($domain->name);

            // Maintain Log depends on the response in new Table

            // Update the domain status in your local database if needed
            $domain->delete();

            return redirect()->back()->with('success', 'Domain deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function updateDates()
    {
        if ($new = request('value')) {
            try {
                if ($virtualminDomain = VirtualminDomain::find(request('domain_id'))) {
                    if (request('column_name') == 'start_date') {
                        $virtualminDomain->start_date = $new;
                    }
                    if (request('column_name') == 'expiry_date') {
                        $virtualminDomain->expiry_date = $new;
                    }

                    $virtualminDomain->save();

                    return respJson(200, 'Successfully updated.');
                }
            } catch (\Exception $e) {
                return respJson(404, $e->getMessage());
            }

            return respJson(404, 'No data found.');
        }

        return respJson(400, 'Value is required.');
    }

    public function domainShow(Request $request)
    {
        $perPage = 5;

        $histories = VirtualminDomainHistory::with(['user'])
        ->where('Virtual_min_domain_id', $request->id)
        ->latest()
        ->paginate($perPage);

        $html = view('virtualmin-domain.domain-history-modal-html')->with('domainHistories', $histories)->render();

        return response()->json(['code' => 200, 'data' => $histories, 'html' => $html, 'message' => 'Content render']);
    }

    public function managecloudDomain(Request $request, $id)
    {   
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            /*$keyword = $request->get('keyword');
            $status = $request->get('status');*/

            // data search action
            $domainsDnsRecords = VirtualminDomainDnsRecords::where('Virtual_min_domain_id', $id);

            /*if (! empty($keyword) || isset($keyword)) {
                $domains = $domains->where('name', 'LIKE', '%' . $keyword . '%');
            }
            if (! empty($status) || isset($status)) {
                $domains = $domains->where('is_enabled', $status);
            }*/

            $domainsDnsRecords = $domainsDnsRecords->paginate(10);

            return view('virtualmin-domain.managecloud', ['domainsDnsRecords' => $domainsDnsRecords, 'domain' => $domain]);

        } catch (\Exception $e) {
            return Redirect::route('virtualmin-domain.index')->with('error', $e->getMessage());
        }
    }
}
