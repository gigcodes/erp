<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Old;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use View;

class OldController extends Controller
{
    /**
     * Defining scope of variable
     *
     * @access protected
     *
     * @var    array $old
     */
    protected $old;

    /**
     * Create a new controller instance.
     *
     * @param mixed $old get old model
     *
     * @return void
     */
    public function __construct(Old $old)
    {
        $this->old = $old;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!empty($_GET['sr_no'])) {
            $sr_no = $_GET['sr_no'];
            $old = $this->old::where('serial_no', $sr_no)->paginate(10)->setPath('');
            $pagination = $old->appends(
                array(
                    'sr_no' => Input::get('sr_no'),
                )
            );
        } else if (!empty($_GET['status'])) {
            $status = $_GET['status'];
            $old = $this->old::where('status', $status)->paginate(5)->setPath('');
            $pagination = $old->appends(
                array(
                    'status' => Input::get('status'),
                )
            );
        } else {
            $old = $this->old->paginate(10);
        }
        $status = $this->old->getStatus();
        return view('old.index', compact('status', 'old'));
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
        $this->validate(
            $request, [
                'name' => 'required',
                'description' => 'required',
                'amount' => 'required',
                'commitment' => 'required',
                'communication' => 'required',
                'status' => 'required',
                'email' => 'required',
                'number' => 'required',
                'address' => 'required',
            ]
        );
        $this->old->saveRecord($request);
        Session::flash('success', 'Record Created');
        return Redirect::back();
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
    public function edit($serial_no)
    {        
        $old = $this->old::where('serial_no', $serial_no)->first();
        $status = $this->old->getStatus();
        return view('old.edit', compact('status', 'old'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $serial_no)
    {
        $this->validate(
            $request, [
                'name' => 'required',
                'description' => 'required',
                'amount' => 'required',
                'commitment' => 'required',
                'communication' => 'required',
                'status' => 'required',
                'email' => 'required',
                'number' => 'required',
                'address' => 'required',
            ]
        );
        $this->old->updateRecord($request, $serial_no);
        Session::flash('success', 'Record Updated');
        return redirect('old');
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
