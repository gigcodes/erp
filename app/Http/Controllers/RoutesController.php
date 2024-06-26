<?php

namespace App\Http\Controllers;

use Artisan;
use Session;
use App\Routes;
use Illuminate\Http\Request;

class RoutesController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * List out all the register routes
     * $param String $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Routes::query();
        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        if ($request->search) {
            $request->search = preg_replace('/[\s]+/', '/', $request->search);
            $query           = $query->whereRaw("MATCH(url)AGAINST('" . $request->search . "')")
                ->orWhereRaw("MATCH(page_title, page_description)AGAINST('" . $request->search . "')");
        }
        $routesData = $query->orderBy('id', 'asc')->paginate(25)->appends(request()->except(['page']));

        return view('routes.index', compact('routesData'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Sync the registered routes in DB.
     * It skip if any route entry is already exist
     * $param String $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sync(Request $request)
    {
        Artisan::call('routes:sync');
        Session::flash('message', 'Data Sync Completed!');

        return redirect()->back();
    }

    /**
     * Sync the registered routes in DB.
     * It skip if any route entry is already exist
     * $param String $request
     *
     * @param mixed $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $routes = Routes::find($id);
        if ($request->post('page_title') && $request->post('page_description')) {
            $updateData = ['page_title' => $request->post('page_title'), 'page_description' => $request->post('page_description')];
            Routes::whereId($id)->update($updateData);
            Session::flash('message', 'Data Updated Successfully');

            return redirect()->route('routes.update', [$id]);
        }

        if ($request->post('status')) {
            $updateData = ['status' => $request->post('status')];
            Routes::whereId($id)->update($updateData);

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Status updated successfully']);
        }

        return view('routes.update', compact('routes'));
    }

    public function updateEmailAlert(Request $request)
    {
        $result = null;

        if ($request->post('type') == 'single') {
            $routes              = Routes::find((int) $request->post('id'));
            $routes->email_alert = ($request->post('email_alert') == 'true') ? 1 : 0;
            $result              = $routes->update();
        }

        if ($request->post('type') == 'all') {
            $result = Routes::update(['email_alert' => ($request->post('email_alert') == 'true') ? 1 : 0]);
        }

        return response()->json(['code' => 200, 'data' => $result, 'message' => 'Email alert updated successfully']);
    }
}
