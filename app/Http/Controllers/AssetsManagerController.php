<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AssetsManager;
use App\AssetsCategory;
use DB;
class AssetsManagerController extends Controller
{
    /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$archived = 0;
		if($request->archived == 1)
			$archived = 1;

		$category = DB::table('assets_category')->get();;
		
		$assets = AssetsManager::join('assets_category', function ($join) {
            $join->on('assets_manager.id', '=', 'assets_category.id');
        })->orderBy('assets_manager.id','DESC')->where('archived', $archived)->paginate(10);

		/*print_r($assets);
		exit;*/
		return view('assets-manager.index',compact('assets', 'category'))
			->with('i', ($request->input('page', 1) - 1) * 10);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		
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
			'name' => 'required',
			'asset_type' => 'required',
			'category_id' => 'required',
			'purchase_type' => 'required',
			'payment_cycle' => 'required',
			'amount' => 'required',
		]);

		$othercat = $request->input('other');
		$category_id = $request->input('category_id');
		$catid = '';
		if($othercat != '' && $category_id != ''){
			$dataCat =  DB::table('assets_category')                    
                    ->Where('cat_name', $othercat)
                    ->first();
           
           	if(!empty($dataCat) && $dataCat->id != ''){
           		$catid = $dataCat->id;
           	}
           	else
           	{
           		$catid = DB::table('assets_category')->insertGetId(
				    ['cat_name' =>  $othercat]
				);
           	}
		}	

		$data = $request->except('_token');
		if($catid != '')
		{
			$data['category_id'] = $catid;
		}		
		AssetsManager::create($data);

		return redirect()->route('assets-manager.index')
		                 ->with('success','Assets created successfully');
	}
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	/*public function show($id)
	{
		$assets = AssetsManager::find($id);
		$reply_categories = ReplyCategory::all();
		$users_array = Helpers::getUserArray(User::all());
		$emails = [];

		return view('assets-manager.show', [
		'assets'  => $assets,
		'reply_categories'  => $reply_categories,
		'users_array'  => $users_array,
		'emails'  => $emails
		]);
	}*/


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		
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
			'name' => 'required',
			'asset_type' => 'required',
			'category_id' => 'required',
			'purchase_type' => 'required',
			'payment_cycle' => 'required',
			'amount' => 'required',
		]);

		$othercat = $request->input('other');
		$category_id = $request->input('category_id');
		$catid = '';
		if($othercat != '' && $category_id != ''){
			$dataCat =  DB::table('assets_category')                    
                    ->Where('cat_name', $othercat)
                    ->first();
           
           	if(!empty($dataCat) && $dataCat->id != ''){
           		$catid = $dataCat->id;
           	}
           	else
           	{
           		$catid = DB::table('assets_category')->insertGetId(
				    ['cat_name' =>  $othercat]
				);
           	}
		}	

		$data = $request->except('_token');
		if($catid != '')
		{
			$data['category_id'] = $catid;
		}
		AssetsManager::find($id)->update($data);

		return redirect()->route('assets-manager.index')
		                 ->with('success','Assets updated successfully');
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$data['archived'] = 1;
		AssetsManager::find($id)->update($data);

		return redirect()->route('assets-manager.index')
		                 ->with('success','Assets deleted successfully');
	}

	public function addNote($id, Request $request) {
        $assetmanager = AssetsManager::findOrFail($id);
        $notes = $assetmanager->notes;
        if (!is_array($notes)) {
            $notes = [];
        }

        $notes[] = $request->get('note');
        $assetmanager->notes = $notes;
        $assetmanager->save();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
