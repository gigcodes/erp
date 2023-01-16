<?php

namespace App\Http\Controllers;

use App\CsvTranslator;
use App\CsvTranslatorHistory;
use App\Exports\CsvTranslatorExport;
use App\Imports\CsvTranslatorImport;
use App\Models\CsvPermissions;
use Illuminate\Http\Request;
use App\User;

class CsvTranslatorController extends Controller
{
    public function index(Request $request)
    {
        $cols = array();
        $allCsvPermission = CsvPermissions::where('user_id',\Auth::user()->id)->get();
        $lang = array();
        array_push($lang,array('data'=>'id'));
        array_push($lang,array('data'=>'key'));
        $permissions = array();
        foreach($allCsvPermission as $permission){
            $cols[] = $permission['lang_id'];
            $lang[] = array('data'=>$permission['lang_id']);
            $permissions[$permission['lang_id']][] = $permission['action'];
        }
        
        $lang =  json_encode($lang);
        $colums = implode(",",$cols);
        $colums = str_replace(",","','",$colums);
        // $colums = "'.$colums'"
        // dd($colums);
        $res = explode(",",$colums);
        if ($request->ajax()) {
            $data = Csvtranslator::latest()->get();
            
            if(!auth()->user()->isAdmin()){
            return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);

           
            }else{
                if($colums){
                    $data = CsvTranslator::select(
                    "status_en",
                    "status_es",
                    "status_ru",
                    "status_ko",
                    "status_ja",
                    "status_it",
                    "status_de",
                    "status_fr",
                    "status_nl",
                    "status_zh",
                    "status_ar",
                    "status_ur",
                    "id",
                    "key",
                    "en",
                    "ru",
                    )->get();
                    // dd($data->toArray());
                    if($data){
                        return datatables()->of($data)
                        ->addIndexColumn()
                        ->editColumn('en', function ($data) use ($permissions)  {
                            $this->commonServiceCheck($permissions,$data,"en");
                        })
                        ->editColumn('es', function($data) use ($permissions){
                            $this->commonServiceCheck($permissions,$data,"es");
                        })
                        ->editColumn('ru', function($data) use ($permissions) {
                            $this->commonServiceCheck($permissions,$data,"ru");
                        })
                        ->editColumn('ko', function($data) use ($permissions) {
                            $this->commonServiceCheck($permissions,$data,"ko");  
                        })
                        ->editColumn('ja', function($data) use ($permissions) {
                            $this->commonServiceCheck($permissions,$data,"ja");   
                        })
                        ->editColumn('it', function($data) use ($permissions) {
                            $this->commonServiceCheck($permissions,$data,"it");   
                        })
                        ->editColumn('de', function($data) use ($permissions) {   
                            $this->commonServiceCheck($permissions,$data,"de");
                        })
                        ->editColumn('fr', function($data) use ($permissions) {
                            $this->commonServiceCheck($permissions,$data,"fr");
                        })
                        ->editColumn('nl', function($data) use ($permissions){
                            $this->commonServiceCheck($permissions,$data,"nl");
                        })
                        ->editColumn('zh', function($data) use ($permissions){
                            $this->commonServiceCheck($permissions,$data,"zh");    
                        })
                        ->editColumn('ar', function($data) use ($permissions){
                            $this->commonServiceCheck($permissions,$data,"ar");  
                        })
                        ->editColumn('ur', function($data) use ($permissions){
                            $this->commonServiceCheck($permissions,$data,"ur");
                        })
                        ->escapeColumns([])
                        ->make(true);
                    }else{
                        return datatables()->of($data)
                        ->make(true);
                    }
                }else{
                    return datatables()->of($data)
                    ->make(true);
                }

        }
    }

        return view('csv-translator.index',compact('lang'));
    }

    public function upload(Request $request)
    {
        \Excel::import(new CsvTranslatorImport(), $request->file);
        \Session::flash('message', 'Successfully imported');
    }

    public function commonServiceCheck($permissions,$data,$lang){
        $data =  $data->toArray();
        $key = $data['key'];
        $id = $data['id'];
        $language = $data[$lang]; 
        
        if(count($permissions[$lang]) == 1){
            if(isset($permissions[$lang]) && isset($permissions[$lang][0]) && $permissions[$lang][0] == 'view'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }else{
                    return   '<div class="bg-custom-grey p-4">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }
            }
            
            if(isset($permissions[$lang]) && isset($permissions[$lang][0]) && $permissions[$lang][0] == 'edit'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='editbtn_model' data-value='$language' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }else{
                    return  '<div class="bg-custom-grey p-3">'.$language ."<a href='#' class='editbtn_model' data-value='$language' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }     
            }    
        }

        if(count($permission[$lang]) == 2){
            if(isset($permissions[$lang]) && isset($permissions[$lang][0]) && $permissions[$lang][0] == 'view'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }else{
                    return   '<div class="bg-custom-grey p-4">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }
            }
            
            if(isset($permissions[$lang]) && isset($permissions[$lang][0]) && $permissions[$lang][0] == 'edit'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='editbtn_model' data-value='.$language.' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }else{
                    return  '<div class="bg-custom-grey p-3">'.$language ."<a href='#' class='editbtn_model' data-value='.$language.' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }     
            }
            
            if(isset($permissions[$lang]) && isset($permissions[$lang][1]) && $permissions[$lang][1] == 'view'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }else{
                    return   '<div class="bg-custom-grey p-4">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }
            }
            
            if(isset($permissions[$lang]) && isset($permissions[$lang][1]) && $permissions[$lang][1] == 'edit'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='editbtn_model' data-value='$language' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }else{
                    return  '<div class="bg-custom-grey p-3">'.$language ."<a href='#' class='editbtn_model' data-value='$language' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }     
            }
        }
    
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
     
        if($request->lang_id == 'en'){
            $record->en = $request->update_record;
            $record->status_en = "checked";
        }
        if($request->lang_id == 'es'){
            $record->es = $request->update_record;
            $record->status_es = "checked";
        }
        if($request->lang_id == 'ru'){
            $record->ru = $request->update_record;
            $record->status_ru = "checked";
        }
        if($request->lang_id == 'ko'){
            $record->ko = $request->update_record;
            $record->status_ko = "checked";
        }
        if($request->lang_ja == 'ja'){
            $record->en = $request->update_record;
            $record->status_ja = "checked";
        }
        if($request->lang_id == 'it'){
            $record->it = $request->update_record;
            $record->status_it = "checked";
        }
        if($request->lang_id == 'de'){
            $record->de = $request->update_record;
            $record->status_de = "checked";
        }
        if($request->lang_id == 'fr'){
            $record->fr = $request->update_record;
            $record->status_fr = "checked";
        }
        if($request->lang_id == 'nl'){
            $record->nl = $request->update_record;
            $record->status_nl = "checked";
        }
        if($request->lang_id == 'zh'){
            $record->zh = $request->update_record;
            $record->status_zh = "checked";
        }
        if($request->lang_id == 'ar'){
            $record->ar = $request->update_record;
            $record->status_ar = "checked";
        }
        if($request->lang_id == 'ur'){
            $record->ur = $request->update_record;
            $record->status_ur = "checked";
        }
        $record->update();

        $historyData = [];
        $historyData['csv_translator_id'] = $record->id;
        $historyData['updated_by_user_id'] = $request->update_by_user_id;
        $historyData['approved_by_user_id'] = $request->update_by_user_id;
        $historyData['key'] = $key;
        $historyData["status_".$request->lang_id]  = $oldStatus;
        $historyData[$request->lang_id] = $oldRecord;
        $historyData['created_at'] = \Carbon\Carbon::now();
        CsvTranslatorHistory::insert($historyData);

        return redirect()->route('csvTranslator.list')->with(['success' => 'Successfully Updated']);
    }

    public function history(Request $request)
    {
        $key = $request->key;
        $language = $request->language;
        $history =  CsvTranslatorHistory::where([
            'csv_translator_id' => $request->id,
            'key'=>$request->key,
        ])->whereRaw('status_'.$request->language.' is not null')->get();
        if(count($history) > 0){
            foreach($history as $key=> $historyData){
                $history[$key]['updater'] = User::where('id',$historyData['updated_by_user_id'])->pluck('name')->first();
                $history[$key]['approver'] = User::where('id',$historyData['updated_by_user_id'])->pluck('name')->first();
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
            
            if(isset($userId)){
                $query->where('updated_by_user_id',$userId);
                $query->orwhere('approved_by_user_id',$userId);
            }
            if(isset($lang)){
                $query->whereNotNull($lang);
            }
            if(isset($status)){
                $query->where('status_'.$lang,$status);
            }
            
            return datatables()
                ->eloquent($query)
                ->toJson();
        }
    }


    public function userPermissions(Request $request){
        if ($request->ajax()) {
            $data = $request->only('user_id','lang_id','action');
            CsvPermissions::insert($data);
            $data =  CsvPermissions::where('user_id',\Auth::user()->id)->get();
            return response()->json(['status' => 200,'data']);
        }
    }
}
