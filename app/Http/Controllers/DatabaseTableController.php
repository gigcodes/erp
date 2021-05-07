<?php

namespace App\Http\Controllers;

use App\DatabaseTableHistoricalRecord;
use Illuminate\Http\Request;

class DatabaseTableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        if($id){
            $databaseHis = DatabaseTableHistoricalRecord::where('database_id',$id)
                ->crossJoin('database_historical_records', 'database_table_historical_records.database_id', '=', 'database_historical_records.id')
                ->select('database_table_historical_records.*', 'database_historical_records.database_name as database');
        }else{
            $databaseHis = DatabaseTableHistoricalRecord::latest()
            ->crossJoin('database_historical_records', 'database_table_historical_records.database_id', '=', 'database_historical_records.id')
                ->select('database_table_historical_records.*', 'database_historical_records.database_name as database');    
        }
        
        $databaseHis = $databaseHis->paginate(20);

        $page = $databaseHis->currentPage();
        //return $databaseHis;

        if ($request->ajax()) {
            $tml = (string) view("databasetable.partial.list", compact('databaseHis', 'page'));
            return response()->json(["code" => 200, "tpl" => $tml, "page" => $page]);
        }

        return view('database.tables', compact('databaseHis','page'));
    }

    public function states(Request $request)
    {

        return view('databasetable.states');

    }

    public function processList()
    {
        return response()->json(["code" => 200 , "records" => \DB::select("show processlist")]);
    }

    public function processKill(Request $request)
    {
        $id = $request->get("id");
        return response()->json(["code" => 200 , "records" => \DB::statement("KILL $id")]);
    }
}