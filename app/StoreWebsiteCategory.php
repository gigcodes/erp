<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\Models\ReplyLog;
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

    public function getPlatformId($storeId, $categoryId, $store_code)
    {
        $result = $this->where(['store_website_id' => $storeId, 'category_id' => $categoryId, 'store_code' => $store_code])->first();

        if (! empty($result)) {
            return $result->remote_id;
        }

        return false;
    }

    public function storeAndGetPlatformId($store_website_id, $categoryId, $storeValue, $url, $api_token, $replyId = 0)
    {
        // \Log::info('Category Id generating');

        $categoryDetails = \App\ReplyCategory::find($categoryId);

        $faqCategoryName = $categoryDetails->name ?? 'Question?';

        $faqParentCategoryId = 0;
        if ($categoryDetails->parent_id) {
            $faqParentCategoryId = $this->getPlatformId($store_website_id, $categoryDetails->parent_id, $storeValue);
            if(!$faqParentCategoryId) {
                $faqParentCategoryId = 0;
            }
        }

        $dataPost = "{\n        \"faq_category_name\": \"$faqCategoryName??\",\n        \"faq_parent_category_id\": $faqParentCategoryId,\n        \"faq_category_description\": \"Answer!!\"\n}";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url . '/' . $storeValue . '/rest/V1/faqcategory');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);

        $headers = [];
        $headers[] = 'Authorization: Bearer ' . $api_token;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        \Log::info(print_r(['API DETAIL Category', $url . '/' . $storeValue . '/rest/V1/faqcategory',$api_token,$dataPost,$result],true));
        (new ReplyLog)->addToLog($replyId, 'Logging faq category result ' . $result . 'for ' . $url . ' dataPost ' . $dataPost . ' with ID ' . $store_website_id . ' on store ' . $storeValue . ' ', 'PushFAQCategory');
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            return false;
        }
        curl_close($ch);

        try {
            $result = json_decode($result);
            $result = json_decode($result);

            //save store website category
            $this->category_id = $categoryId;
            $this->remote_id = $result->category_id;
            $this->store_website_id = $store_website_id;
            $this->store_code = $storeValue;
            $this->save();

            return $result->category_id;
        } catch(\Exception $e) {
            return false;
        }
    }
    
}
