<?php

namespace App\Http\Controllers;

use App\Account;
use App\Brand;
use App\ColdLeads;
use App\Product;
use Illuminate\Http\Request;

class ColdLeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $leads = ColdLeads::all()->map(function($lead) {
           $lead->products = Product::whereHas('brands', function ($query) use ($lead) {
               $query->whereIn('name', explode(' ', strtoupper($lead->because_of)));
           })->orderBy('created_at', 'DESC')->take(4)->get();
           return $lead;
       });

       $accounts = Account::all();


       return view('cold_leads.index', compact('leads', 'accounts'));
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
     * @param  \App\ColdLeads  $coldLeads
     * @return \Illuminate\Http\Response
     */
    public function show(ColdLeads $coldLeads)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ColdLeads  $coldLeads
     * @return \Illuminate\Http\Response
     */
    public function edit(ColdLeads $coldLeads)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ColdLeads  $coldLeads
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ColdLeads $coldLeads)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
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
}
