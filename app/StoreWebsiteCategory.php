<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteCategory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="remote_id",type="integer")
     * @SWG\Property(property="category_name",type="string")
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $fillable = [
        'category_id', 'remote_id', 'store_website_id', 'created_at', 'updated_at', 'category_name',
    ];


    function    getPlatformId($storeId, $categoryId, $store_code){
        $result     =   $this->where(['store_website_id' => $storeId, 'category_id' => $categoryId, 'store_code' => $store_code])->first();

        if(!empty($result)){
            return $result->remote_id;
        }
        return false;
    }


    function    storeAndGetPlatformId($store_website_id, $categoryId, $storeValue, $url, $api_token){

        // \Log::info('Category Id generating');

        $categoryDetails    =   \App\ReplyCategory::find($categoryId);

        $faqCategoryName    =   $categoryDetails->name ?? 'Question?';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url. "/" . $storeValue .'/rest/V1/faqcategory');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n        \"faqCategoryName\": \"$faqCategoryName??\",\n        \"faqCategoryDescription\": \"Answer!!\"\n}");


        $headers = array();
        $headers[] = 'Authorization: Bearer '.$api_token;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        try{
            $result = json_decode($result);
            $result = json_decode($result);

            //save store website category
            $this->category_id          =   $categoryId;
            $this->remote_id            =   $result->category_id;
            $this->store_website_id     =   $store_website_id;
            $this->store_code           =   $storeValue;
            $this->save();


            return $result->category_id;
        }
        catch(\Exception $e){
            return false;   
        }

        
    }
}
