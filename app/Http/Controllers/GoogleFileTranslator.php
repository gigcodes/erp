<?php

namespace App\Http\Controllers;

use File;
use Exception;
use App\Language;
use App\Translations;
use App\GoogleTranslate;
use Illuminate\Http\Request;
use App\GoogleFiletranslatorFile;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use App\Models\GoogleTranslateUserPermission;
use App\Models\GoogleTranslateCsvDataImport;
use App\Models\GoogleTranslateCsvData;
use App\Models\GoogleFileTranslateHistory;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class GoogleFileTranslator extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $query = GoogleFiletranslatorFile::query();
        if ($request->term) {
            $query = $query->where('name', 'LIKE', '%' . $request->term . '%');
        }

        $data = $query->orderBy('id', 'asc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googlefiletranslator.partials.list-files', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }

        return view('googlefiletranslator.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Language = Language::all();

        return view('googlefiletranslator.create', compact('Language'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'tolanguage' => 'required',
            'file' => 'required|max:10000|mimes:csv,txt',
        ]);
        try {
            $this->getMediaPathSave();
            $filename = $request->file('file');
            $ext = $filename->getClientOriginalExtension();
            //$filenameNew = md5($filename).'.'.$ext;
            $filenameNew = null;
            $media = MediaUploader::fromSource($request->file('file'))
            ->toDestination('uploads', 'google-file-translator')
            ->upload();

            if (isset($media) && isset($media->filename) && isset($media->extension)) {
                $filenameNew = $media->filename . '.' . $media->extension;
            } else {
                throw new Exception('Error while uploading file.');
            }
            $input = $request->all();
            $input['name'] = $filenameNew;
            $insert = GoogleFiletranslatorFile::create($input);

            $path = public_path() . '/uploads/google-file-translator/';
            $languageData = Language::where('id', $insert->tolanguage)->first();
           

            if (file_exists($path . $insert->name)) {
                try {
                    $result = $this->translateFile($path . $insert->name, $languageData->locale, ',');
                foreach ($result as $translationSet) {
                    try {

                        $translationDataStored = new GoogleTranslateCsvData();
                        $translationDataStored->key = $translationSet[0];
                        $translationDataStored->value = $translationSet[1];
                        $translationDataStored->standard_value = $translationSet[2];
                        $translationDataStored->google_file_translate_id = $insert->id;
                        $translationDataStored->save();
                   
                    } catch (\Exception $e) {
                        return 'Upload failed: ' . $e->getMessage();
                    }
                }

                } catch (\Exception $e) {
                    return redirect()->route('googlefiletranslator.list')->with('error', $e->getMessage());
                }
            } else {
                throw new Exception('File not found');
            }

            return redirect()->route('googlefiletranslator.list')->with('success', 'Translation created successfully');
        } catch (\Exception $e) {
            \Log::error($e);

            return redirect()->route('googlefiletranslator.list')->with('error', 'Error while uploading file. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $record = GoogleTranslateCsvData::find($request->record_id);
        $oldRecord = $record->standard_value;
        $oldStatus = $record->status;
       
        $record->updated_by_user_id = $request->update_by_user_id;
        $record->standard_value = $request->update_record;
        $record->status = 1;
        $record->save();

        $history = new GoogleFileTranslateHistory();
        $history->old_value = $oldRecord;
        $history->new_value =  $request->update_record;
        $history->updated_by = $request->update_by_user_id;
        $history->status =  $oldStatus;
        $history->google_file_translate_csv_data_id = $request->record_id;
        $history->save();

        return response()->json(['status' => 200, 'data' => $record, 'message' => "Value edited Successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $GoogleFiletranslatorFile = GoogleFiletranslatorFile::find($id);
        $path = public_path() . '/uploads/google-file-translator/';
        if (file_exists($path . $GoogleFiletranslatorFile->name)) {
            unlink($path . $GoogleFiletranslatorFile->name);
        }
        $GoogleFiletranslatorFile->delete();

        return redirect()->route('googlefiletranslator.list')
            ->with('success', 'Translation deleted successfully');
    }

    public function download($file)
    {
        $path = public_path() . '/uploads/google-file-translator/';
        if (file_exists($path . $file)) {
            $headers = [
                'Content-Type: text/csv',
            ];

            return response()->download($path . $file, $file, $headers);
        }
    }

    public function getMediaPathSave()
    {
        $path = public_path() . '/uploads/google-file-translator/';
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

        return $path;
    }

    public function translateFile($path, $language, $delimiter = ',')
    {
        if (! file_exists($path) || ! is_readable($path)) {
            return false;
        }
        $newCsvData = [];
        $keywordToTranslate = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Check translation SEPARATE LINE exists or not
                $checkTranslationTable = Translations::select('text')->where('to', $language)->where('text_original', $data[0])->first();
                if ($checkTranslationTable) {
                    $data[] = htmlspecialchars_decode($checkTranslationTable->text, ENT_QUOTES);
                } else {
                    $keywordToTranslate[] = $data[0];
                    $data[] = $data[0];
                }
                $newCsvData[] = $data;
            }
            fclose($handle);
        }

        $translateKeyPair = [];
        if (isset($keywordToTranslate) && count($keywordToTranslate) > 0) {
            // Max 128 lines supports for translation per request
            $keywordToTranslateChunk = array_chunk($keywordToTranslate, 100);
            $translationString = [];
            foreach ($keywordToTranslateChunk as $key => $chunk) {
                try {
                    $googleTranslate = new GoogleTranslate();
                    $result = $googleTranslate->translate($language, $chunk, true);
                } catch (\Exception $e) {
                    \Log::channel('errorlog')->error($e);
                    throw new Exception($e->getMessage());
                }
                // $translationString = [...$translationString, ...$result];
                array_push($translationString, ...$result);
            }

            $insertData = [];
            if (isset($translationString) && count($translationString) > 0) {
                foreach ($translationString as $key => $value) {
                    $translateKeyPair[$value['input']] = $value['text'];
                    $insertData[] = [
                        'text_original' => $value['input'],
                        'text' => $value['text'],
                        'from' => 'en',
                        'to' => $language,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (! empty($insertData)) {
                Translations::insert($insertData);
            }
        }

        // Update the csv with Translated data
        if (isset($newCsvData) && count($newCsvData) > 0) {
            for ($i = 0; $i < count($newCsvData); $i++) {
                $last = array_pop($newCsvData[$i]);
                array_push($newCsvData[$i], htmlspecialchars_decode($translateKeyPair[$last] ?? $last));
            }

            $handle = fopen($path, 'r+');
            foreach ($newCsvData as $line) {
                fputcsv($handle, $line, $delimiter, $enclosure = '"');
            }
            fclose($handle);
        }

        return $newCsvData;
    }

    public function dataViewPage($id, $type)
    {
        if($type == "googletranslate")
        {
            $googleTranslateDatas=  GoogleTranslateCsvData::Where('google_file_translate_id',$id)->latest()->get();
        } else {
            $lang = explode('-', $type);
            $lang = end($lang);

            if (preg_match('/-([a-zA-Z]{2})\.csv$/', $type, $matches)) {
                $lang = $matches[1];
            }
            $getLang = Language::Where('locale', $lang)->first();

            $googleTranslateDatas=  GoogleTranslateCsvData::Where('storewebsite_id',$id)->where('lang_id',$getLang->id)->latest()->get();
        }

        return View('googlefiletranslator.googlefiletranlate-list', ['id' => $id, 'googleTranslateDatas' => $googleTranslateDatas]);
    }

    public function downloadPermission(Request $request)
    {
        $googleTranslate = GoogleFiletranslatorFile::find($request->id);
        $googleTranslate->download_status = 1 ;
        $googleTranslate->save();

        return response()->json(['status' => 200, 'data' => $googleTranslate, 'message' => "download permision allowed"]);

    }

    public function userViewPermission(Request $request)
    {
        $googleFiletranslatorPermission = new GoogleTranslateUserPermission();
        $googleFiletranslatorPermission->google_translate_id =  $request->user_id;
        $googleFiletranslatorPermission->user_id =  $request->user_id;
        $googleFiletranslatorPermission->lang_id =  $request->lang_id;
        $googleFiletranslatorPermission->action =   $request->action;
        $googleFiletranslatorPermission->type =   $request->type;
        $googleFiletranslatorPermission->save();

        return response()->json(['status' => 200, 'data' => $googleFiletranslatorPermission, 'message' => "download permision allowed"]);

    }

    public function tranalteHistoryShow($id)
    {
        try {
            $google_file_translate_csv_data_id = [];
            if (isset($id)) {
                $google_file_translate_csv_data_id = GoogleFileTranslateHistory::with(['user'])->Where('google_file_translate_csv_data_id', $id)->latest()->get();

                return response()->json([
                    'status' => true,
                    'data' => $google_file_translate_csv_data_id,
                    'message' => 'history added successfully',
                    'status_name' => 'success',
                ], 200);
        
            } else {
                throw new Exception('Task not found');
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $google_file_translate_csv_data_id,
                'message' => 'history added successfully',
                'status_name' => 'failed',
            ], 500);
    
        }
    }

    public function statusChange(Request $request)
    {
        $googleTranslateDatas =  GoogleTranslateCsvData::find(($request->id));
        $google_file_translate_csv_data_id = GoogleFileTranslateHistory::with(['user'])->Where('google_file_translate_csv_data_id', $request->id)
                                            ->latest('updated_at') 
                                            ->first(); 
        $oldvalue = $google_file_translate_csv_data_id->old_value;

        if($request->status == 'accept')
        {
            $googleTranslateDatas->status = 2;
            $googleTranslateDatas->approved_by_user_id = \Auth::id();
            $googleTranslateDatas->save();

            return response()->json([
                'status' => true,
                'data' => $googleTranslateDatas,
                'message' => 'Update successfully',
                'status_name' => 'success',
            ], 200);
        }
           
        if($request->status == 'reject')
        {
            $googleTranslateDatas->status = 2;
            $googleTranslateDatas->standard_value = $oldvalue;
            $googleTranslateDatas->save();

            return response()->json([
                'status' => false,
                'data' => $googleTranslateDatas,
                'message' => 'Rejected successfully',
                'status_name' => 'failed',
            ], 500);
        }
       
    }


    public function downloadCsv($id, $type)
    {
        $csv = ''; // Initialize the $csv variable as an empty string

        if ($type == "googletranslate") {
            $googleTranslateDatas = GoogleTranslateCsvData::where('google_file_translate_id', $id)->latest()->get();
        } else {
            $lang = explode('-', $type);
            $lang = end($lang);
        
            if (preg_match('/-([a-zA-Z]{2})\.csv$/', $type, $matches)) {
                $lang = $matches[1];
            }
            $getLang = Language::where('locale', $lang)->first();
        
            $googleTranslateDatas = GoogleTranslateCsvData::where('storewebsite_id', $id)
                ->where('lang_id', $getLang->id)
                ->latest()
                ->get();
        }
        
        $fileName = 'google_translate_data.csv';
        
        // Add a header row with column names
        // $csv .= "value,standard_value\n";
        $csvContent = '"StanardValue","value"' . "\n";

        foreach ($googleTranslateDatas as $data) {
            // Add data from each column to the corresponding column in the CSV
            // $csv .= "{$data->value},{$data->standard_value}\n";
            $csvContent .= $this->formatForCSV($data->standard_value) . ','
            . $this->formatForCSV($data->value) . "\n";
        }

       
        
        // Set the content type and disposition for download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        // Optionally, you can set other headers like cache control if needed
        $headers['Cache-Control'] = 'must-revalidate, post-check=0, pre-check=0';
        $headers['Expires'] = '0';
        $headers['Pragma'] = 'public';
        
        // Send the file as a download response
        return Response::make($csvContent, 200, $headers);
        
    }

    public function formatForCSV($value)
    {
        // If the value contains a comma or a double quote, enclose it in double quotes and escape existing double quotes.
        if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }
}
