<?php

namespace App\Http\Controllers;

use App\BulkCustomerRepliesKeyword;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers;

class BulkCustomerRepliesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        set_time_limit(0);
        $keywords = BulkCustomerRepliesKeyword::where('is_manual', 1)->get();
        $autoKeywords = BulkCustomerRepliesKeyword::where('count', '>', 10)
            ->whereNotIn('value', [
                'test', 'have', 'sent', 'the', 'please', 'pls', 'through', 'using', 'solo', 'that',
                'comes', 'message', 'sending', 'Yogesh', 'Greetings', 'this', 'numbers', 'maam', 'from',
                'changed', 'them', 'with' , '0008000401700', 'WhatsApp', 'send', 'Auto', 'based', 'suggestion',
                'Will', 'your', 'number', 'number,', 'messages', 'also', 'meanwhile'
            ])
            ->take(25)
            ->orderBy('count', 'DESC')
            ->get();

        $searchedKeyword = null;

        if ($request->get('keyword_filter')) {
            $keyword = $request->get('keyword_filter');

            $searchedKeyword = BulkCustomerRepliesKeyword::where('value', $keyword)->first();

        }
        $groups           = \App\QuickSellGroup::select('id', 'name', 'group')->orderby('id', 'DESC')->get();
        $pdfList = [];
        $nextActionArr = DB::table('customer_next_actions')->pluck('name', 'id');
        $reply_categories = \App\ReplyCategory::orderby('id', 'DESC')->get();
        $settingShortCuts = [
            "image_shortcut"      => \App\Setting::get('image_shortcut'),
            "price_shortcut"      => \App\Setting::get('price_shortcut'),
            "call_shortcut"       => \App\Setting::get('call_shortcut'),
            "screenshot_shortcut" => \App\Setting::get('screenshot_shortcut'),
            "details_shortcut"    => \App\Setting::get('details_shortcut'),
            "purchase_shortcut"   => \App\Setting::get('purchase_shortcut'),
        ];
        $users_array      = Helpers::getUserArray(\App\User::all());

        return view('bulk-customer-replies.index', compact('keywords','autoKeywords', 'searchedKeyword', 'nextActionArr','groups','pdfList','reply_categories','settingShortCuts','users_array'));
    }

    public function storeKeyword(Request $request) {
        $this->validate($request, [
            'keyword' => 'required'
        ]);

        $type = 'keyword';
        $numOfSpaces = count(explode(' ', $request->get('keyword')));
        if ($numOfSpaces > 1 && $numOfSpaces < 4) {
            $type = 'phrase';
        } else if ($numOfSpaces >= 4) {
            $type = 'sentence';
        }

        $keyword = new BulkCustomerRepliesKeyword();
        $keyword->value = $request->get('keyword');
        $keyword->text_type = $type;
        $keyword->is_manual = 1;
        $keyword->count = 0;
        $keyword->save();

        return redirect()->back()->with('message', title_case($type) . ' added successfully!');
    }

    public function sendMessagesByKeyword(Request $request) {
        $this->validate($request, [
            'message' => 'required',
            'customers' => 'required'
        ]);

        foreach ($request->get('customers') as $customer) {
            $myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add([
                'message' => $request->get('message'),
                'customer_id' => $customer,
                'status' => 1
            ]);
            
            app('App\Http\Controllers\WhatsAppController')->sendMessage($myRequest, 'customer');

            DB::table('bulk_customer_replies_keyword_customer')->where('customer_id', $customer)->delete();
        }

        return redirect()->back()->with('message', 'Messages sent successfully!');

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
