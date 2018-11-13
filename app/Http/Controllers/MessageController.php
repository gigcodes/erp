<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Message;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $messages = Message::with(['user'])->get();

        return response()->json($messages);
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
        $message = $this->validate(request(), [
              'body' => 'required',
              'moduleid' => 'required',
              'moduletype' => 'required',
              'status' => 'required',
            ]);
            $data = $request->except( '_token');
            $id = $request->get('moduleid');
            $moduletype = $request->get('moduletype');
            if ($_FILES["image"]["size"] > 10) {

                   $target_dir = "uploads/";
                   $target_file = $target_dir . basename($_FILES["image"]["name"]);
                    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                    $msgtxt = $request->get('body');
                    $msgtxt .= ' <a target="_blank" href="/'.$target_file.'" class="message-img" />'.$target_file.'</a>';
                    $data['body'] = $msgtxt;
             }

            $data['userid'] = Auth::id();
            Message::create($data);


            if( $data['status'] == '1' ) {

	            NotificationQueueController::createNewNotification( [
		            'message'    => 'New : ' . $data['body'],
		            'timestamps' => [ '+0 minutes' ],
		            'model_type' => $data['moduletype'],
		            'model_id'   => $data['moduleid'],
		            'user_id'    => \Auth::id(),
		            'sent_to'    => '',
		            'role'       => 'Admin',
	            ] );

	            NotificationQueueController::createNewNotification( [
		            'message'    => 'New : ' . $data['body'],
		            'timestamps' => [ '+0 minutes' ],
		            'model_type' => $data['moduletype'],
		            'model_id'   => $data['moduleid'],
		            'user_id'    => \Auth::id(),
		            'sent_to'    => '',
		            'role'       => 'Supervisors',
	            ] );
            }
            else if($data['status'] == '0'){

	            NotificationQueueController::createNewNotification( [
		            'message'    => 'Customer Reply : ' . $data['body'],
		            'timestamps' => [ '+0 minutes' ],
		            'model_type' => $data['moduletype'],
		            'model_id'   => $data['moduleid'],
		            'user_id'    => \Auth::id(),
		            'sent_to'    => $data['assigned_user'],
	            ] );

              NotificationQueueController::createNewNotification( [
		            'message'    => 'Customer Reply : ' . $data['body'],
		            'timestamps' => [ '+0 minutes' ],
		            'model_type' => $data['moduletype'],
		            'model_id'   => $data['moduleid'],
		            'user_id'    => \Auth::id(),
		            'sent_to'    => '',
		            'role'       => 'Admin',
	            ] );
            } else if($data['status'] == '4'){

	            NotificationQueueController::createNewNotification( [
		            'message'    => 'New Instructions : ' . $data['body'],
		            'timestamps' => [ '+0 minutes' ],
		            'model_type' => $data['moduletype'],
		            'model_id'   => $data['moduleid'],
		            'user_id'    => \Auth::id(),
		            'sent_to'    => $data['assigned_user'],
	            ] );
            }

           return redirect('/'. $moduletype.'/'.$id);
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
        // $message = Message::find($request->get('messageid'));
       $message = Message::find($id);
        // $this->validate(request(), [
        //   'body' => 'required',
        //
        // ]);
         $message->body = $request->get('body');
         // $moduleid = $request->get('moduleid');
         // $moduletype = $request->get('moduletype');
         // if ($_FILES["image"]["size"] > 10) {
         //           $target_dir = "uploads/";
         //           $target_file = $target_dir . basename($_FILES["image"]["name"]);
         //           move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
         //           $msgtxt = $request->get('body');
         //            $msgtxt .= ' <a href="/'.$target_file.'" class="message-img" />'.$target_file.'</a>';
         //            $message->body = $msgtxt;
         //     }

         $message->save();
         return response(['message' => 'Success']);
         // return redirect('/'. $moduletype.'/'.$moduleid);
    }

    public function updatestatus(Request $request )
    {

        $message = Message::find($request->get('id'));
        $message->status = $request->get('status');
        $moduleid = $request->get('moduleid');
        $moduletype = $request->get('moduletype');
        $message->save();


	    if( $message->status == '2' ) {

		    NotificationQueueController::createNewNotification( [
			    'message'    => 'Approved : ' . $message->body,
			    'timestamps' => [ '+0 minutes' ],
			    'model_type' => $message->moduletype,
			    'model_id'   => $message->moduleid,
			    'user_id'    => Auth::id(),
			    'sent_to'    => '',
			    'role'       => 'message',
		    ] );
	    }

	    return redirect('/'. $moduletype.'/'.$moduleid);
    }

    public function loadmore(Request $request)
    {
         $moduleid = $request->get('moduleid');
         $moduletype = $request->get('moduletype');
         $messageid = $request->get('messageid');
         $messages = Message::all()->where('id','<',$messageid)->where('moduleid','=', $moduleid)->where('moduletype','=', $moduletype)->sortByDesc("created_at")->take(2)->toArray();
         return view('leads.bubbles',compact('messages'));
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
