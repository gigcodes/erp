<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;
class ResourceImage extends Model{

  protected $fillable = ['cat_id','image1'];

  public function category(){
    return $this->belongsTo('App\ResourceCategory', 'id');
  }


  static public function create($input){
    $resourceimg = new ResourceImage;
    $resourceimg->cat_id = $input['parent_id'];
    $resourceimg->image1 = @$input['image1'];
    $resourceimg->image2 = @$input['image2'];
    $resourceimg->url = @$input['url'];
    $resourceimg->description = @$input['description'];
    $resourceimg->created_at = date("Y-m-d H:i:s");
    $resourceimg->updated_at = date("Y-m-d H:i:s");
    $resourceimg->created_by = Auth::user()->name;
    return $resourceimg->save();
  	// echo "<pre>"; print_r($resourceimg);die("herer");
  }

  static public function getData(){
    $allresources = ResourceImage::get();
    $dataArray=array();
    $title="";
    if($allresources){
      foreach ($allresources as $key => $resources) {
        $categories = ResourceCategory::where('id','=',$resources->cat_id)->get()->first();
        $parent_id = $categories->parent_id;
        $id = $categories->id;
        if($parent_id == 0){
          $title = $categories->title;
        }else{
          $titlestr=array();
          while ($parent_id != 0) {
            $categories = ResourceCategory::where('id','=',$id)->get()->first();
            $titlestr[]= $categories->title;
            $id = $parent_id = $categories->parent_id;
          }
          krsort($titlestr);
          $title=implode(" >> ", $titlestr);
        }
        $dataArray[]=array('id'=>$resources->id,
                           'cat'=>$title,
                           'cat_id'=>$resources->cat_id,
                           'url' => $resources->url,
                           'description' => $resources->description,
                           'created_at'=>$resources->created_at,
                           'updated_at'=>$resources->updated_at,
                           'created_by'=>$resources->created_by);
      }
    }
    return $dataArray;
    // echo "<pre>"; print_r($dataArray); die;
  }

}
