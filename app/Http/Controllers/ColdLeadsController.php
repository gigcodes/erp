<?php

namespace App\Http\Controllers;

use App\Account;
use App\Customer;
use App\ColdLeads;
use Illuminate\Http\Request;

class ColdLeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! $request->isXmlHttpRequest()) {
            if (isset($request->via)) {
                $via = $request->via;
            } else {
                $via = '';
            }

            return view('cold_leads.index', compact('via'));
        }

        $this->validate($request, [
            'pagination' => 'required|integer',
        ]);

        if (strlen($request->get('query')) >= 4) {
            $query = $request->get('query');
            $leads = ColdLeads::where('status', '>', 0)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%$query%");
                    $q->where('username', 'LIKE', "%$query%");
                });
        } else {
            $leads = ColdLeads::where('status', '>', 0);
        }

        if ($request->get('gender') == 'm' || $request->get('gender') == 'f' || $request->get('gender') == 'o') {
            $leads = $leads->where('gender', $request->get('gender'));
        }

        if ($request->get('acc') > 0) {
            $leads = $leads->where('account_id', $request->get('acc'));
        }

        $leads = $leads->orderBy('updated_at', 'DESC')->with('account')->paginate($request->get('pagination'));

        $accounts = Account::where('platform', 'instagram')->where('broadcast', 1)->get();

        return response()->json([
            'leads'    => $leads,
            'accounts' => $accounts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ColdLeads $coldLeads)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ColdLeads $coldLeads)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ColdLeads $coldLeads)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     *
     * @return void
     */
    public function destroy($id)
    {
        $lead = ColdLeads::find($id);

        if ($lead) {
            $lead->delete();
        }

        return redirect()->back()->with('message', 'Cold lead deleted successfully!');
    }

    public function sendMessage($leadId, Request $request)
    {
        $lead = ColdLeads::find($leadId);

        $senderUsername = env('IG_USERNAME');
        $password       = env('IG_PASSWORD');

        $receiverId = $lead->platform_id;

        $message = $request->get('message');

        if (strlen($receiverId) < 5) {
            $receiverId = $lead->username;
        }

        $messageType = 1;
    }

    public function addToCustomer($leadId)
    {
    }

    public function deleteColdLead(Request $request)
    {
        $leadId = $request->get('lead_id');
        $dl     = ColdLeads::findOrFail($leadId);
        $dl->forceDelete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function showImportedColdLeads(Request $request)
    {
        $leads = ColdLeads::where('is_imported', 1);

        if ($request->get('address') !== '') {
            $leads = $leads->where(function ($query) use ($request) {
                $query->where('address', 'LIKE', $request->get('address'))
                    ->orWhere('name', 'LIKE', $request->get('address'))
                    ->orWhere('username', 'LIKE', $request->get('address'))
                    ->orWhere('platform_id', 'LIKE', $request->get('address'));
            });
        }

        $query = $request->get('address');

        $leads = $leads->paginate(200);

        return view('leads.imported_index', compact('leads', 'query'));
    }

    public function addLeadToCustomer(Request $request)
    {
        $this->validate($request, [
            'cold_lead_id' => 'required',
        ]);

        $lead = ColdLeads::find($request->get('cold_lead_id'));

        if ($lead) {
            $customer                  = new Customer();
            $customer->name            = $lead->name;
            $customer->phone           = $lead->platform_id;
            $customer->whatsapp_number = $lead->platform_id;
            $customer->city            = $lead->address;
            $customer->country         = 'IN';
            $customer->save();

            $lead->customer_id = $customer->id;
            $lead->save();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function home(Request $request)
    {
        if (strlen($request->get('query')) >= 4) {
            $query = $request->get('query');
            $leads = ColdLeads::where('status', '>', 0)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%$query%");
                    $q->where('username', 'LIKE', "%$query%");
                });
        } else {
            $leads = ColdLeads::where('status', '>', 0);
        }

        if ($request->get('gender') == 'm' || $request->get('gender') == 'f' || $request->get('gender') == 'o') {
            $leads = $leads->where('gender', $request->get('gender'));
        }

        if ($request->get('acc') > 0) {
            $leads = $leads->where('account_id', $request->get('acc'));
        }

        $leads = $leads->orderBy('updated_at', 'DESC')->with('account')->paginate($request->get('pagination'));

        $accounts = Account::where('platform', 'instagram')->where('broadcast', 1)->get();

        return view('instagram.direct-message.index', compact('leads', 'accounts'));
    }
}
