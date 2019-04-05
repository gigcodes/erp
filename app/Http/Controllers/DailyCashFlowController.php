<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DailyCashFlow;
use App\Setting;

class DailyCashFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $short_fall = 0;
      $cash_flows = DailyCashFlow::latest()->paginate(Setting::get('pagination'));

      foreach ($cash_flows as $cash_flow) {
        $short_fall += $cash_flow->received - $cash_flow->expected;
      }

      return view('dailycashflows.index', [
        'cash_flows'  => $cash_flows,
        'short_fall'  => $short_fall
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'received_from' => 'sometimes|nullable|string',
        'paid_to'       => 'sometimes|nullable|string',
        'date'          => 'required',
        'expected'      => 'required_without:received|nullable|numeric',
        'received'      => 'required_without:expected|nullable|numeric',
      ]);

      $data = $request->except('_token');

      DailyCashFlow::create($data);

      return redirect()->route('dailycashflow.index')->withSuccess('You have successfully stored a record!');
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
      $this->validate($request, [
        'received_from' => 'sometimes|nullable|string',
        'paid_to'       => 'sometimes|nullable|string',
        'date'          => 'required',
        'expected'      => 'required_without:received|nullable|numeric',
        'received'      => 'required_without:expected|nullable|numeric',
      ]);

      $data = $request->except('_token');

      DailyCashFlow::find($id)->update($data);

      return redirect()->route('dailycashflow.index')->withSuccess('You have successfully updated a record!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $cash_flow = DailyCashFlow::find($id);

      $cash_flow->delete();

      return redirect()->route('dailycashflow.index')->withSuccess('You have successfully deleted a record!');
    }
}
