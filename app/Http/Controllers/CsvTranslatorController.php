<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CsvTranslator;
use App\Imports\CsvTranslatorImport;
use App\Exports\CsvTranslatorExport;
use App\CsvTranslatorHistory;
use Redirect;

class CsvTranslatorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CsvTranslator::select('*');
            return datatables()
                ->eloquent($query)
                ->toJson();
        }
        return view('csv-translator.index');
    }

    public function upload(Request $request){
        \Excel::import(new CsvTranslatorImport(), $request->file);
        \Session::flash('message', 'Successfully imported');
    }

     public function exportData(Request $request){
        \Excel::import(new CsvTranslatorImport(), $request->file);
        \Session::flash('message', 'Successfully imported');
    }

    public function export(){
         return \Excel::download(new CsvTranslatorExport, 'users.xlsx');
    }

    public function update(Request $request){
        $record = CsvTranslator::find($request->record_id);
        $oldRecord = $record->{$request->lang_id};
        $key = $record->key;
        $record->updated_by_user_id =  $request->update_by_user_id;
        $record->{$request->lang_id} = $request->update_record;
        $record->update();
        
        $historyData = array();
        $historyData['csv_translator_id'] = $record->id;
        $historyData['updated_by_user_id'] = $request->update_by_user_id;
        $historyData['key'] = $key;
        $historyData[$request->lang_id] = $oldRecord;
        CsvTranslatorHistory::insert($historyData);
        return Redirect::back()->with(['success' => 'Successfully Updated']);

    }
}
