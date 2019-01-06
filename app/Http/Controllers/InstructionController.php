<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instruction;
use App\Setting;
use Carbon\Carbon;

class InstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $instructions = Instruction::latest()->paginate(Setting::get('pagination'));

      return view('instructions.index')->withInstructions($instructions);
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
        'instruction' => 'required|min:3',
        'customer_id' => 'required|numeric'
      ]);

      $instruction = new Instruction;
      $instruction->instruction = $request->instruction;
      $instruction->customer_id = $request->customer_id;

      $instruction->save();

      return back()->with('success', 'You have successfully created instruction!');
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

    public function complete(Request $request)
    {
      $instruction = Instruction::find($request->id);
      $instruction->completed_at = Carbon::now();
      $instruction->save();

      return response("$instruction->completed_at");
    }

    public function pending(Request $request)
    {
      $instruction = Instruction::find($request->id);
      $instruction->pending = 1;
      $instruction->save();

      return response("success");
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
