<?php

namespace App\Http\Controllers;

use App\CsvTranslator;
use App\CsvTranslatorHistory;
use App\Exports\CsvTranslatorExport;
use App\Imports\CsvTranslatorImport;
use App\User;
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

        if ($request->lang_id == 'en') {
            $record->en = $request->update_record;
            $record->status_en = 'checked';
        }
        if ($request->lang_id == 'es') {
            $record->es = $request->update_record;
            $record->status_es = 'checked';
        }
        if ($request->lang_id == 'ru') {
            $record->ru = $request->update_record;
            $record->status_ru = 'checked';
        }
        if ($request->lang_id == 'ko') {
            $record->ko = $request->update_record;
            $record->status_ko = 'checked';
        }
        if ($request->lang_ja == 'ja') {
            $record->en = $request->update_record;
            $record->status_ja = 'checked';
        }
        if ($request->lang_id == 'it') {
            $record->it = $request->update_record;
            $record->status_it = 'checked';
        }
        if ($request->lang_id == 'de') {
            $record->de = $request->update_record;
            $record->status_de = 'checked';
        }
        if ($request->lang_id == 'fr') {
            $record->fr = $request->update_record;
            $record->status_fr = 'checked';
        }
        if ($request->lang_id == 'nl') {
            $record->nl = $request->update_record;
            $record->status_nl = 'checked';
        }
        if ($request->lang_id == 'zh') {
            $record->zh = $request->update_record;
            $record->status_zh = 'checked';
        }
        if ($request->lang_id == 'ar') {
            $record->ar = $request->update_record;
            $record->status_ar = 'checked';
        }
        if ($request->lang_id == 'ur') {
            $record->ur = $request->update_record;
            $record->status_ur = 'checked';
        }
        $record->update();

        $historyData = [];
        $historyData['csv_translator_id'] = $record->id;
        $historyData['updated_by_user_id'] = $request->update_by_user_id;
        $historyData['approved_by_user_id'] = $request->update_by_user_id;
        $historyData['key'] = $key;
        $historyData['status_'.$request->lang_id] = $oldStatus;
        $historyData[$request->lang_id] = $oldRecord;
        $historyData['created_at'] = \Carbon\Carbon::now();
        CsvTranslatorHistory::insert($historyData);

        return redirect()->route('csvTranslator.list')->with(['success' => 'Successfully Updated']);
    }

    public function history(Request $request)
    {
        $key = $request->key;
        $language = $request->language;
        $history = CsvTranslatorHistory::where([
            'csv_translator_id' => $request->id,
            'key' => $request->key,
        ])->whereRaw('status_'.$request->language.' is not null')->get();
        if (count($history) > 0) {
            foreach ($history as $key => $historyData) {
                $history[$key]['updater'] = User::where('id', $historyData['updated_by_user_id'])->pluck('name')->first();
                $history[$key]['approver'] = User::where('id', $historyData['updated_by_user_id'])->pluck('name')->first();
            }
        }

        return response()->json(['status' => 200, 'data' => $history]);
    }

    public function filterCsvTranslator(Request $request)
    {
        if ($request->ajax()) {
            $userId = $request->user;
            $lang = $request->lang;
            $status = $request->status;
            $query = CsvTranslator::select('*');

            if (isset($userId)) {
                $query->where('updated_by_user_id', $userId);
                $query->orwhere('approved_by_user_id', $userId);
            }
            if (isset($lang)) {
                $query->whereNotNull($lang);
            }
            if (isset($status)) {
                $query->where('status_'.$lang, $status);
            }

            return datatables()
                ->eloquent($query)
                ->toJson();
        }
    }
}
