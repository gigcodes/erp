<?php

namespace App\Http\Controllers;

use App\Setting;
use App\StoreWebsite;
use App\ApiResponseMessage;
use App\ApiResponseMessagesTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ApiResponseMessageValueHistory;

class ApiResponseMessageController extends Controller
{
    public function index(Request $request)
    {
        $api = ApiResponseMessage::with(['storeWebsite']);
        if ($request->store_website_id != '') {
            $api->where('store_website_id', $request->store_website_id);
        }
        if ($request->api_key != '') {
            $api->where('key', 'LIKE', '%' . $request->api_key . '%');
        }
        if ($request->api_value != '') {
            $api->where('value', 'LIKE', '%' . $request->api_value . '%');
        }
        $api_response = $api->orderBy('created_at', 'desc')->paginate(Setting::get('pagination'));
        $store_websites = StoreWebsite::orderBy('created_at', 'desc')->get();
        if ($request->ajax()) {
            $page = $request->page;
            $count = ($page - 1) * 15;

            return view('apiResponse/index_ajax', compact('api_response', 'store_websites', 'count'));
        } else {
            return view('apiResponse/index', compact('api_response', 'store_websites'));
        }
    }

    public function store(Request $request)
    {
        $duplicate = ApiResponseMessage::where('store_website_id', $request->store_website_id)->where('key', $request->res_key)->first();
        if (! empty($duplicate)) {
            \Session::flash('message', 'Key already exists for the selected store website');
            \Session::flash('alert-class', 'alert-danger');

            return redirect()->route('api-response-message');
        }

        $response = new ApiResponseMessage();
        $response->store_website_id = $request->store_website_id;
        $response->key = $request->res_key;
        $response->value = $request->res_value;
        if ($response->save()) {
            \Session::flash('message', 'Added successfully');
            \Session::flash('alert-class', 'alert-success');

            return redirect()->route('api-response-message');
        } else {
            \Session::flash('message', 'Something went wrong');
            \Session::flash('alert-class', 'alert-danger');

            return redirect()->route('api-response-message');
        }
    }

    public function getEditModal(Request $request)
    {
        $id = $request->id;
        $store_websites = StoreWebsite::orderBy('created_at', 'desc')->get();
        $data = ApiResponseMessage::where('id', $id)->first();
        $history = ApiResponseMessageValueHistory::where('api_response_message_id', $id)->orderBy('created_at', 'desc')->first();
        $returnHTML = view('apiResponse/ajaxEdit')->with('data', $data)->with('store_websites', $store_websites)->with('history', $history)->render();

        return response()->json(['data' => $returnHTML, 'type' => 'success'], 200);
    }
    
    public function lodeTranslation(Request $request)
    {
        $id = $request->id;
        $apiResponseMessage = ApiResponseMessage::where('id', $id)->first();
        $returnHTML="<tr><td colspan='5'>No Data Found!</td></tr>";
       
        if($apiResponseMessage){
            $translations=ApiResponseMessagesTranslation::where('store_website_id', $apiResponseMessage->store_website_id)->where('key', $apiResponseMessage->key)->get();
            
            if(!$translations->isEmpty()){
                $returnHTML="";
                foreach($translations as $translation){
                    $returnHTML.="<tr>";
                    $returnHTML.="<td>".$translation->id."</td>";
                    $returnHTML.="<td>".$translation->storeWebsite->title."</td>";
                    $returnHTML.="<td>".$translation->lang_code."</td>";
                    $returnHTML.="<td>".$translation->key."</td>";
                    $returnHTML.="<td>".$translation->value."</td>";
                    $returnHTML.="</tr>";
                }
            }
        }
        

        return response()->json(['data' => $returnHTML, 'type' => 'success'], 200);
    }

    public function update(Request $request)
    {
        $old_value = '';
        $response = ApiResponseMessage::where('id', $request->id)->first();
        $old_value = $response->value;
        $response->store_website_id = $request->store_website_id;
        $response->key = $request->key;
        $response->value = $request->value;
        if ($response->save()) {
            $data = [
                'user_id' => Auth::User()->id,
                'api_response_message_id' => $response->id,
                'old_value' => $old_value,
                'new_value' => $request->value,
            ];
            \App\ApiResponseMessageValueHistory::insert($data);
            \Session::flash('message', 'Updated successfully');
            \Session::flash('alert-class', 'alert-success');

            return response()->json(['type' => 'success'], 200);
        } else {
            \Session::flash('message', 'Something went wrong');
            \Session::flash('alert-class', 'alert-danger');

            return redirect()->route('api-response-message');
        }
    }

    public function destroy($id)
    {
        if (ApiResponseMessage::where('id', $id)->delete()) {
            \Session::flash('message', 'Deleted successfully');
            \Session::flash('alert-class', 'alert-success');

            return redirect()->route('api-response-message');
        } else {
            \Session::flash('message', 'Something went wrong');
            \Session::flash('alert-class', 'alert-danger');

            return redirect()->route('api-response-message');
        }
    }
}
