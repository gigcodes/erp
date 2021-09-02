<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Semrush;

class SemrushController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Domain Report';
        return view('semrush.domain_report', compact('title'));
    }

    public function keyword_report()
    {
        $title = 'Keyword Report';
        return view('semrush.keyword_report', compact('title'));
    }

    public function url_report()
    {
        $title = 'URL Report';
        return view('semrush.url_report', compact('title'));
    }

    public function backlink_reffring_report()
    {
        $title = 'Backlink & Reffring Domain';
        return view('semrush.backlink_reffring_report', compact('title'));
    }

    public function publisher_display_ad()
    {
        $title = 'Publisher Display Ad';
        return view('semrush.publisher_display_ad', compact('title'));
    }

    public function traffic_analitics_report()
    {
        $title = 'Traffic analitics Report';
        return view('semrush.traffic_analitics_report', compact('title'));
    }

    public function competitor_analysis()
    {
        $title = 'Competitor analyasis';
        return view('semrush.competitor_analysis', compact('title'));
    }

    public function manageSemrushAccounts()
    {
       // $all_accounts = Semrush::where(['status' => 1])->get();
        //return view('semrush.manage-accounts', compact('all_accounts'));
        return view('semrush.manage-accounts');
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
