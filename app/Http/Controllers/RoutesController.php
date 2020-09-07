<?php
namespace App\Http\Controllers;

use App\Routes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers;
use DB;
use Session;

class RoutesController extends Controller
{
	function __construct()
	{
		//
	}


	/**
	 * List out all the register routes
	 * $param String $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$query = Routes::query();
		if($request->id){
			$query = $query->where('id', $request->id);
		}
		if($request->search){
			$query = $query->where('url', 'LIKE','%'.$request->search.'%')->orWhere('page_title', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('page_description', 'LIKE', '%'.$request->search.'%');
		}

		$routesData = $query->orderBy('id', 'asc')->paginate(25)->appends(request()->except(['page']));
		/*if ($request->ajax()) {
            return response()->json([
                'tbody' => view('users.partials.list-users', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string)$data->render(),
                'count' => $data->total(),
            ], 200);
        }*/
		/*echo "<pre>";
		print_r($data);
		exit;*/
		
		return view('routes.index', compact('routesData'))
			->with('i', ($request->input('page', 1) - 1) * 5);
			
			
			
			
	}

	
	/**
	 * Sync the registered routes in DB.
	 * It skip if any route entry is already exist
	 * $param String $request
	 * @return \Illuminate\Http\Response
	 */
	/*public function sync(Request $request)
	{
		$routes = $this->getRoutesByMethod("GET");
		foreach ($routes as $route ){
			if (Routes::where('url', '=', $route->uri)->count() > 0) 
			{	
				continue;
			}
			$uriNo[] = $route->uri;
			Routes::create(['url' => $route->uri]);
		}
		exit;
		//return view ('urldata.index',compact('routes'));
	}*/
	
	/**
	 * Get all the register route
	 * $param String $method
	 * @return \Illuminate\Http\Response
	 */
	private function getRoutesByMethod($method){
		$routes = \Route::getRoutes()->getRoutesByMethod();
		return $routes[$method];
	}
	
	
	/**
	 * Sync the registered routes in DB.
	 * It skip if any route entry is already exist
	 * $param String $request
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$routes = Routes::find($id);
		if($request->post('page_title') && $request->post('page_description'))
		{
			$updateData = array('page_title'=>$request->post('page_title'), 'page_description'=>$request->post('page_description'));
			Routes::whereId($id)->update($updateData);
			Session::flash('message', 'Data Updated Successfully'); 
		}
		return view ('routes.update',compact('routes'));
	}
	
}
