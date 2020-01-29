<?php

namespace App\Http\Controllers\Marketing;

use App\MailinglistTemplate;
use App\MailingTemplateFile;
use Composer\Package\Archiver\ZipArchiver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use ZipArchive;


class MailinglistTemplateController extends Controller
{
    public function index () {

            $mailings = MailinglistTemplate::paginate(20);
            return view("marketing.mailinglist.templates.index",compact('mailings'));

    }
    public function ajax(Request $request) {

        $query = MailinglistTemplate::query();

        if ($request->term) {
            $query->where('name', 'LIKE', '%' . $request->term . '%')
                ->orWhere('image_count', 'LIKE', '%' . $request->term . '%')
                ->orWhere('text_count', 'LIKE', '%' . $request->term . '%');
        }
        if ($request->date) {
            $query->where('created_at', 'LIKE', '%' . $request->date . '%');
        }
        $query = $query->get();


        return response()->json([
            'mailings' => view('partials.mailing-template.list',[
                'mailings' => $query
            ])->render()
        ]);


    }
    public function store (Request $request) {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image_count' => 'required|numeric',
            'text_count' => 'required|numeric',
            'image' => 'required|image',
     /*       'file' => 'required|image',*/
        ]);

        if ($validator->fails()) {
            return response()->json([ 'errors' => $validator->getMessageBag()->toArray()]);
        }

        $mailing_item = new MailinglistTemplate();
        $mailing_item->name = $data['name'];
        $mailing_item->image_count = $data['image_count'] ;
        $mailing_item->text_count = $data['text_count'];
        $mailing_item->save();

        $path = "mailinglist/email-templates/".$mailing_item->id;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $filename = date('U') . str_random(10);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $path = $path . "/" . $filename . "." . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
            $mailing_item->example_image = $path;
            $mailing_item->save();
        }
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if($ext == 'zip'){
            $zip_upload_path = 'zip_file_'.$mailing_item->id.'.zip';
            move_uploaded_file($_FILES['file']['tmp_name'],$zip_upload_path);

            $zip = new ZipArchive;
            $res = $zip->open($zip_upload_path);
            if($res === true){

                $zip_path = 'email-templates/'.$mailing_item->id;
                if (!file_exists($zip_path)) {
                    mkdir($zip_path, 0777, true);
                }
                for($i=0; $i<$zip->numFiles; $i++){
                    $name =  $zip->statIndex($i)['name'];
                    MailingTemplateFile::create([
                        'mailing_id' => $mailing_item->id,
                        'path' => $zip_path.'/'.$name
                    ]);
                }
                $zip->extractTo($zip_path);
                $zip->close();
            }
            if(file_exists($zip_upload_path)){
                unlink($zip_upload_path);
            }
        }else{
            $another_path = 'email-templates/'.$mailing_item->id;


            if (!file_exists($another_path)) {
                mkdir($another_path, 0777, true);
            }
            $filename = date('U') . str_random(10);
            $another_path = $another_path . "/" . $filename . "." . $ext;
            move_uploaded_file($_FILES['file']['tmp_name'],$another_path);
            MailingTemplateFile::create([
                'mailing_id' => $mailing_item->id,
                'path' => $another_path
            ]);
        }

        return response()->json([
            'item' => view('partials.mailing-template.store',[
                'item' => $mailing_item
            ])->render()
        ]);
    }

}

/*
$table->increments('id');
$table->string("name");
$table->unsignedInteger("image_count");
$table->unsignedInteger("text_count");
$table->text("example_image");
$table->timestamps();*/