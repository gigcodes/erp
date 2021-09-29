<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use App\MarketingMessageCustomer;
use App\MessagingGroup;
use App\MessagingGroupCustomer;
use App\MarketingMessage;
use App\StoreWebsite;


class TwillioSmsController extends Controller{


    public function index() {
        $data = MessagingGroup::orderBy('id', 'desc')->paginate(15);
        $websites = StoreWebsite::all();

        return view('twillio_sms.index', compact('data','websites'));
    }


    public function createMailinglist(Request $request) {
        
        $this->validate($request, [
            'name' => 'required',
            'store_website_id' => 'required',
            'service_id' => 'required',
        ]);

        $data = MessagingGroup::create([
            'name' => $request->name,
            'store_website_id' => $request->store_website_id,
            'service_id' => $request->service_id,
        ]);
        
        return response()->json([
            $data
        ]);
    }

    public function destroy (Request $request) {
        Service::destroy($request->id);

        return response()->json([
            $request->id
        ]);

    }

    public function update (Request $request) {

        $updated = Service::findOrFail($request->id);

        $updated->name = $request->name;
        $updated->description  = $request->description;
        $updated->save();

        $data = Service::findOrFail($request->id);

        return response()->json([
            $data
        ]);
    }


}
