<?php

namespace App\Http\Controllers;

use App\BulkCustomerRepliesKeyword;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkCustomerRepliesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keywords = BulkCustomerRepliesKeyword::where('is_manual', 1)->get();
        $autoKeywords = BulkCustomerRepliesKeyword::where('count', '>', 10)->get();

        $searchedKeyword = null;

        if ($request->get('keyword_filter')) {
            $keyword = $request->get('keyword_filter');

            $searchedKeyword = BulkCustomerRepliesKeyword::where('value', $keyword)->with(['customers', 'customers.messageHistory'])->first();

        }

        return view('bulk-customer-replies.index', compact('keywords','autoKeywords', 'searchedKeyword'));
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

    public function sendMessagesByKeyword() {

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
