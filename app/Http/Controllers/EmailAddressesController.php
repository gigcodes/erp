<?php

namespace App\Http\Controllers;

use App\EmailAddress;
use App\StoreWebsite;
use App\EmailRunHistories;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmailAddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $emailAddress = EmailAddress::paginate(15);
		$allStores = StoreWebsite::all();
        return view('email-addresses.index', [
            'emailAddress' => $emailAddress,
			'allStores' => $allStores
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'from_name' => 'required|string|max:255',
            'from_address' => 'required|string|max:255',
            'driver' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|string|max:255',
            'encryption' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ]);

        $data = $request->except('_token');
        
        EmailAddress::create($data);

        return redirect()->route('email-addresses.index')->withSuccess('You have successfully saved a Email Address!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'from_name' => 'required|string|max:255',
            'from_address' => 'required|string|max:255',
            'driver' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|string|max:255',
            'encryption' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ]);

        $data = $request->except('_token');
        
        EmailAddress::find($id)->update($data);

        return redirect()->back()->withSuccess('You have successfully updated a Email Address!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $emailAddress = EmailAddress::find($id);

        $emailAddress->delete();

        return redirect()->route('email-addresses.index')->withSuccess('You have successfully deleted a Email Address');
    }

    public function getEmailAddressHistory(Request $request){
		$EmailHistory = EmailRunHistories::where('email_run_histories.email_address_id', $request->id)
        ->whereDate('email_run_histories.created_at',Carbon::today())
        ->join('email_addresses', 'email_addresses.id', 'email_run_histories.email_address_id')
        ->select(['email_run_histories.*','email_addresses.from_name'])->get();
		$history = '';
		if(sizeof($EmailHistory) > 0) {
			foreach ($EmailHistory as $runHistory) {
				$status = ($runHistory->is_success == 0) ? "Failed" : "Success";
				$message = empty($runHistory->message) ? "-" : $runHistory->message;
				$history .= '<tr>
				<td>'.$runHistory->id.'</td>
				<td>'.$runHistory->from_name.'</td>
				<td>'.$status.'</td>
				<td>'.$message.'</td>
				<td>'.$runHistory->created_at->format('Y-m-d H:i:s').'</td>
				</tr>';
			}
		} else {
			$history .= '<tr>
					<td colspan="5">
						No Result Found
					</td>
				</tr>';
		}
		
		return response()->json(['data' => $history]);
    }
}
