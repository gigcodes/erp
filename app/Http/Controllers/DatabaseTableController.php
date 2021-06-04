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
        
        if($request->table_name){
            $databaseHis = $databaseHis->where('database_table_historical_records.database_name','like','%'.$request->table_name.'%');
        }
        $databaseHis = $databaseHis->orderBy('database_table_historical_records.size', 'desc');
        $databaseHis = $databaseHis->paginate(20);

        $page = $databaseHis->currentPage();
        //return $databaseHis;

        if ($request->ajax()) {
            $tml = (string) view("database.partial.list-table", compact('databaseHis', 'page'));
            return response()->json(["code" => 200, "tpl" => $tml, "page" => $page]);
        }

        return view('database.tables', compact('databaseHis','page'));
    }
}