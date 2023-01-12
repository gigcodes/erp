<?php

namespace App\Http\Controllers;

use App\CsvTranslator;
use App\CsvTranslatorHistory;
use App\Exports\CsvTranslatorExport;
use App\Imports\CsvTranslatorImport;
use Illuminate\Http\Request;

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

    public function upload(Request $request)
    {
        \Excel::import(new CsvTranslatorImport(), $request->file);
        \Session::flash('message', 'Successfully imported');
    }

     public function exportData(Request $request)
     {
         \Excel::import(new CsvTranslatorImport(), $request->file);
         \Session::flash('message', 'Successfully imported');
     }

    public function export()
    {
        return \Excel::download(new CsvTranslatorExport, 'csv-translator.xlsx');
    }

    public function update(Request $request)
    {
        $record = CsvTranslator::find($request->record_id);
        $oldRecord = $record->{$request->lang_id};
        $oldStatus = $record->status;
        $key = $record->key;
        $record->updated_by_user_id = $request->update_by_user_id;
        $record->approved_by_user_id = $request->update_by_user_id;
        $record->{$request->lang_id} = $request->update_record;
        $record->status = 'checked';
        $record->update();

        $historyData = [];
        $historyData['csv_translator_id'] = $record->id;
        $historyData['updated_by_user_id'] = $request->update_by_user_id;
        $historyData['approved_by_user_id'] = $request->update_by_user_id;
        $historyData['key'] = $key;
        $historyData['status'] = $oldStatus;
        $historyData[$request->lang_id] = $oldRecord;
        CsvTranslatorHistory::insert($historyData);
        return redirect()->route('csvTranslator.list')->with(['success' => 'Successfully Updated']);
    }

    public function history(Request $request)
    {
        $history = CsvTranslatorHistory::where('csv_translator_id', $request->id)->get();

        return response()->json(['status' => 200, 'data' => $history]);
    }
}
