<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 13/10/18
 * Time: 5:52 PM
 */

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class Helpers
{
    public static function getUsersByRoleName($roleName = 'Sales')
    {
        $roleID = Role::findByName($roleName);

        $users = DB::table('users AS u')
            ->select('u.id', 'u.name')
            ->where('m.role_id', '=', $roleID->id)
            ->leftJoin('model_has_roles AS m', 'm.model_id', '=', 'u.id')
            ->distinct()
            ->orderBy('u.name')
            ->get();

        return $users;
    }

    public static function getUsersRoleName($roleName = 'HOD of CRM')
    {
        $roleID = Role::findByName($roleName);

        $users = DB::table('users AS u')
            ->select('u.id', 'u.name')
            ->where('r.role_id', '=', $roleID->id)
            ->leftJoin('role_user AS r', 'r.user_id', '=', 'u.id')
            ->distinct()
            ->orderBy('u.name')
            ->get();

        return $users;
    }

    public static function getUserArray($users)
    {
        return collect($users)->pluck('name', 'id')->toArray();
    }

    public static function getUserNameById($id)
    {
        $user = User::find($id);

        if ($user) {
            return $user->name;
        } else {
            return 'Unkown';
        }
    }

    public static function getUsersArrayByRole($roleName = 'Sales')
    {
        return self::getUserArray(self::getUsersByRoleName($roleName));
    }

    public static function timeAgo($date)
    {
        $timestamp = strtotime($date);

        $strTime = ['second', 'minute', 'hour', 'day', 'month', 'year'];
        $length = ['60', '60', '24', '30', '12', '10'];

        $currentTime = time();
        if ($currentTime >= $timestamp) {
            $diff = time() - $timestamp;
            for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
                $diff = $diff / $length[$i];
            }

            $diff = round($diff);

            return $diff . ' ' . $strTime[$i] . '(s) ago ';
        }
    }

    public static function explodeToArray($item)
    {
        $temp_values = explode(',', $item);
        
        $values = array_reduce($temp_values, function($carry,$size){
            $carry[$size] = $size;
            return $carry;
        },[]);

        return $values;
    }

    public static function getadminorsupervisor()
    {
        $user = Auth::user();
        $myrole = json_decode(json_encode($user->getRoleNames()));
        if (in_array('Supervisors', $myrole) or in_array('Admin', $myrole)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getmessagingrole()
    {
        $user = Auth::user();
        $myrole = json_decode(json_encode($user->getRoleNames()));
        if (in_array('message', $myrole)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getproductsfromarraysofids($productsid)
    {
        $products = json_decode($productsid);
        $productnamearray = [];
        $product = new Product();
        if (! empty($products)) {
            foreach ($products as $productid) {
                $product_instance = $product->find($productid);
                $productnamearray[] = $product_instance->name;
            }
            $productsname = implode(',', $productnamearray);

            return $productsname;
        }

        return '';
    }

    public static function getleadstatus($statusid)
    {
        $status = new status;
        $data['status'] = $status->all();
        foreach ($data['status'] as $key => $value) {
            if ($statusid == $value) {
                return $key;
            }
        }
    }

    public static function getlatestmessage($moduleid, $model_type)
    {
        $messages = DB::table('messages')->where('moduleid', '=', $moduleid)->where('moduletype', $model_type)->orderBy('created_at', 'desc')->first();
        $messages = json_decode(json_encode($messages), true);

        return $messages['body'];
    }

    public static function getAllUserIdsWithoutRole($role = 'Admin')
    {
        $users = User::all();
        $user_ids = [];

        foreach ($users as $user) {
            $user_roles = $user->getRoleNames()->toArray();

            if (! in_array($role, $user_roles)) {
                $user_ids[] = $user->id;
            }
        }

        return $user_ids;
    }

    public static function getUserIdByName($name)
    {
        $user = DB::table('users')->where('name', $name)->first();

        if (! empty($user)) {
            return $user->id;
        }

        return '';
    }

    public static function statusClass($assign_status)
    {
        $task_status = '';

        switch ($assign_status) {
            case 1:
                $task_status = ' accepted ';
                break;

            case 2:
                $task_status = ' postponed ';
                break;

            case 3:
                $task_status = ' rejected ';
                break;
        }

        return $task_status;
    }

    public static function currencies()
    {
        return [
            1 => 'USD',
            'EUR',
            'AED',
            'INR',
        ];
    }

    /**
     * Custom paginator
     *
     * @param  mixed  $request $request        attributes
     * @param  array  $values $values         array values to be paginated
     * @param  mixed  $posts_per_page $posts_per_page posts to show per page
     * @return $items
     */
    public static function customPaginator($request, $values = [], $posts_per_page = '10')
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemCollection = collect($values);
        $perPage = intval($posts_per_page);
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        $items = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);
        $items->setPath($request->url());

        return $items;
    }

    /**
     * Get the final destination of helper
     */
    public static function findUltimateDestination($url, $maxRequests = 10)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        //customize user agent if you desire...
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Link Checker)');

        while ($maxRequests--) {
            //fetch
            curl_setopt($ch, CURLOPT_URL, $url);
            $response = curl_exec($ch);

            //try to determine redirection url
            $location = '';
            if (in_array(curl_getinfo($ch, CURLINFO_HTTP_CODE), [301, 302, 303, 307, 308])) {
                if (preg_match('/Location:(.*)/i', $response, $match)) {
                    $location = trim($match[1]);
                }
            }

            if (empty($location)) {
                //we've reached the end of the chain...
                return $url;
            }

            //build next url
            if ($location[0] == '/') {
                $u = parse_url($url);
                $url = $u['scheme'] . '://' . $u['host'];
                if (isset($u['port'])) {
                    $url .= ':' . $u['port'];
                }
                $url .= $location;
            } else {
                $url = $location;
            }
        }

        return null;
    }

    public static function selectSupplierList($none = true)
    {
        $list = \App\Supplier::pluck('supplier', 'id')->toArray();

        if ($none) {
            return ['' => 'None'] + $list;
        }

        return $list;
    }

    public static function selectCategoryList($defaultVal = false)
    {
        return \App\Category::attr([
            'name' => 'category',
            'class' => 'form-control-sm form-control select2',
            'style' => 'width:200px ',
        ])->selected($defaultVal)->renderAsDropdown();
    }

    public static function selectBrandList($none = true)
    {
        $list = \App\Brand::pluck('name', 'id')->toArray();

        if ($none) {
            return ['' => 'None'] + $list;
        }

        return $list;
    }

    public static function selectStatusList()
    {
        return ['' => 'None'] + \App\Helpers\StatusHelper::getStatus();
    }

    public static function quickSellGroupList($none = true)
    {
        $list = \App\QuickSellGroup::pluck('name', 'id')->toArray();

        if ($none) {
            return ['' => 'None'] + $list;
        }

        return $list;
    }

    public static function getInstagramVars($name)
    {
        $keyword = \App\InfluencerKeyword::where('name', $name)->first();
        $extravars = '';
        if ($keyword) {
            // check keyword account
            $instagram = $keyword->instagramAccount;
            if ($instagram) {
                $extravars = "&ig_uname={$instagram->first_name}&ig_pswd={$instagram->password}";
            }
        }

        return $extravars;
    }

    public static function getFacebookVars($name)
    {
        $keyword = \App\InfluencerKeyword::where('name', $name)->first();
        $extravars = '';
        if ($keyword) {
            // check keyword account
            $instagram = $keyword->instagramAccount;
            if ($instagram) {
                $extravars = "?fb_uname={$instagram->first_name}&fb_pswd={$instagram->password}";
            }
        }

        return $extravars;
    }

    public static function createQueueName($name)
    {
        $name = str_replace([' ', '-'], '_', $name); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', strtolower($name)); // Removes special chars.
    }

    public static function getQueueName($flip = false)
    {
        $content = file_get_contents(public_path('queues.txt'));
        if ($content) {
            $queue = explode(',', $content);
        } else {
            $queue = [];
        }

        if ($flip) {
            $l = [];
            foreach ($queue as $p) {
                $l[$p] = $p;
            }

            return $l;
        }

        return $queue;
    }

    public static function getFromEmail($customer_id = 0)
    {
        if (! empty($customer_id)) {
            $customer = Customer::find($customer_id);
            if ($customer) {
                $emailAddressDetails = EmailAddress::select()->where(['store_website_id' => $customer->store_website_id])->first();
                if ($emailAddressDetails) {
                    return $emailAddressDetails->from_address;
                }
            }
        }

        return config('env.MAIL_FROM_ADDRESS');
    }
    //How to call \App\Helpers::getFromEmail() |  pass custome id if available

    public static function getFromEmailByOrderId($order_id)
    {
        if (! empty($order_id)) {
            $order = Order::find($order_id);
            if ($order) {
                return self::getFromEmail($order->customer->id);
            }
        }

        return config('env.MAIL_FROM_ADDRESS');
    }

    public static function getAudioUrl($messages)
    {
        $reg_exUrl = '/\b(https?|ftp|file|http):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';
        // The Text you want to filter for urls
        $text = 'The text you want to filter goes here. https://example.com';

        if (preg_match($reg_exUrl, $messages, $url)) {
            return array_shift($url);
        }

        return $messages;
    }

    public static function isBase64Encoded($string)
    {
        return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string);
    }

    public static function getTaskUserList($task, $users)
    {
        $users_list = '';
        foreach ($task->users as $key => $user) {
            if ($key != 0) {
                $users_list .= ', ';
            }
            if (array_key_exists($user->id, $users)) {
                $users_list .= $users[$user->id];
            } else {
                $users_list = 'User Does Not Exist';
            }
        }

        $users_list .= ' ';
        foreach ($task->contacts as $key => $contact) {
            if ($key != 0) {
                $users_list .= ', ';
            }
            $users_list .= "$contact->name - $contact->phone" . ucwords($contact->category);
        }

        return $users_list;
    }
}
