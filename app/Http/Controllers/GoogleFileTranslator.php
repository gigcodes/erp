<?php

namespace App\Http\Controllers;

use App\GoogleFiletranslatorFile;
use App\GoogleTranslate;
use App\Language;
use App\Translations;
use Exception;
use File;
use Illuminate\Http\Request;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

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
            $query = $query->where('name', 'LIKE', '%'.$request->term.'%');
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'tolanguage' => 'required',
            'file' => 'required|max:10000|mimes:csv,txt',
        ]);
        $this->getMediaPathSave();
        $filename = $request->file('file');
        $ext = $filename->getClientOriginalExtension();
        //$filenameNew = md5($filename).'.'.$ext;
        $filenameNew = $filename->getClientOriginalName();
        $media = MediaUploader::fromSource($request->file('file'))
        ->toDestination('uploads', 'google-file-translator')
        ->upload();
        $input = $request->all();
        $input['name'] = $filenameNew;
        $insert = GoogleFiletranslatorFile::create($input);
        $path = public_path().'/uploads/google-file-translator/';
        $languageData = Language::where('id', $insert->tolanguage)->first();
        if (file_exists($path.$insert->name)) {
            try {
                $result = $this->translateFile($path.$insert->name, $languageData->locale, ',');
            } catch (\Exception $e) {
                return redirect()->route('googlefiletranslator.list')->with('error', $e->getMessage());
            }
        }

        return redirect()->route('googlefiletranslator.list')->with('success', 'Translation created successfully');
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
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
        $path = public_path().'/uploads/google-file-translator/';
        if (file_exists($path.$GoogleFiletranslatorFile->name)) {
            unlink($path.$GoogleFiletranslatorFile->name);
        }
        $GoogleFiletranslatorFile->delete();

        return redirect()->route('googlefiletranslator.list')
            ->with('success', 'Translation deleted successfully');
    }

    public function download($file)
    {
        $path = public_path().'/uploads/google-file-translator/';
        if (file_exists($path.$file)) {
            $headers = [
                'Content-Type: text/csv',
            ];

            return response()->download($path.$file, $file, $headers);
        }
    }

    public function getMediaPathSave()
    {
        $path = public_path().'/uploads/google-file-translator/';
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

        return $path;
    }

    public function translateFile($path, $language, $delimiter = ',')
    {
        if (! file_exists($path) || ! is_readable($path)) {
            return false;
        }
        $newCsvData = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Check translation SEPARATE LINE exists or not
                $checkTranslationTable = Translations::select('text')->where('to', $language)->where('text_original', $data[0])->first();
                if ($checkTranslationTable) {
                    $data[] = htmlspecialchars_decode($checkTranslationTable->text, ENT_QUOTES);
                } else {
                    try {
                        $googleTranslate = new GoogleTranslate();
                        $translationString = $googleTranslate->translate($language, $data[0], true);

                        // Devtask-2893 : Added model to save individual line translation
                        Translations::addTranslation($data[0], $translationString, 'en', $language);
                        $data[] = htmlspecialchars_decode($translationString, ENT_QUOTES);
                    } catch (\Exception $e) {
                        \Log::channel('errorlog')->error($e);
                        throw new Exception($e->getMessage());
                    }
                }
                $newCsvData[] = $data;
            }
            fclose($handle);
        }
        $handle = fopen($path, 'r+');

        foreach ($newCsvData as $line) {
            fputcsv($handle, $line, $delimiter, $enclosure = '"');
        }
        fclose($handle);
    }
}
