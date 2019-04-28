<?php

namespace App\Http\Controllers;

use App\Complaint;
use App\ComplaintThread;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
        $this->middleware('permission:review-view');
    }

    public function index()
    {
        //
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
        'customer_id' => 'sometimes|nullable|integer',
        'platform'    => 'sometimes|nullable|string',
        'complaint'   => 'required|string|min:3',
        'thread.*'    => 'sometimes|nullable|string',
        'link'        => 'sometimes|nullable|url',
        'date'        => 'required|date'
      ]);

      $data = $request->except('_token');

      $complaint = Complaint::create($data);

      if ($request->thread[0] != null) {
        foreach ($request->thread as $thread) {
          ComplaintThread::create([
            'complaint_id' => $complaint->id,
            'thread'       => $thread
          ]);
        }
      }

      return redirect()->route('review.index')->withSuccess('You have successfully added complaint');
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
        'customer_id' => 'sometimes|nullable|integer',
        'platform'    => 'sometimes|nullable|string',
        'complaint'   => 'required|string|min:3',
        'thread.*'    => 'sometimes|nullable|string',
        'link'        => 'sometimes|nullable|url',
        'date'        => 'required|date'
      ]);

      $data = $request->except('_token');

      $complaint = Complaint::find($id);
      $complaint->update($data);

      if ($request->thread[0] != null) {
        $complaint->threads()->delete();

        foreach ($request->thread as $thread) {
          ComplaintThread::create([
            'complaint_id' => $complaint->id,
            'thread'       => $thread
          ]);
        }
      }

      return redirect()->route('review.index')->withSuccess('You have successfully updated complaint');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $complaint = Complaint::find($id);
      $complaint->threads()->delete();
      $complaint->delete();

      return redirect()->route('review.index')->withSuccess('You have successfully deleted complaint');
    }
}
