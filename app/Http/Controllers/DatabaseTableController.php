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
    public function index(Request $request)
    {
        $databaseHis = DatabaseTableHistoricalRecord::latest();

        $customRange = $request->get("customrange");

        if(!empty($customRange)) {
            $range = explode(" - ",$customRange);
            if(!empty($range[0])) {
               $startDate = $range[0];
            }
            if(!empty($range[1])) {
               $endDate = $range[1];
            }
        }

        if(!empty($startDate)) {
            $databaseHis = $databaseHis->whereDate("created_at",">=",$startDate);
        }

        if(!empty($endDate)) {
            $databaseHis = $databaseHis->whereDate("created_at","<=",$endDate);
        }

        $databaseHis = $databaseHis->paginate(20);

        $page = $databaseHis->currentPage();

        if ($request->ajax()) {
            $tml = (string) view("databasetable.partial.list", compact('databaseHis', 'page'));
            return response()->json(["code" => 200, "tpl" => $tml, "page" => $page]);
        }

        return view('databasetable.index', compact('databaseHis','page'));
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