<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use App\BugType;
use App\Setting;
use App\Website;
use App\BugStatus;
use Carbon\Carbon;
use App\BugSeverity;
use App\scraperImags;
use App\StoreWebsite;
use App\WebsiteStore;
use Dompdf\Exception;
use GuzzleHttp\Client;
use App\BugEnvironment;
use App\SiteDevelopment;
use Illuminate\Http\Request;
use App\SiteDevelopmentCategory;
use Illuminate\Support\Facades\DB;

class scrapperPhyhon extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $images = new scraperImags();
        $images = $images->selectRaw('count(scraper_imags.id) as total,scraper_imags.website_id,store_website , date(scraper_imags.created_at) as date_created_at');
        if ($request->store_website_id > 0) {
            $images = $images->where('store_website', $request->store_website_id);
        }
        if ($request->device != '') {
            $images = $images->where('device', $request->device);
        }

        $images = $images->groupBy('scraper_imags.website_id', 'store_website', DB::raw('date(scraper_imags.created_at)'));
        $images = $images->orderByRaw('scraper_imags.created_at DESC, store_website ASC, scraper_imags.website_id ASC');
        $images = $images->paginate(Setting::get('pagination'));

        foreach ($images as $image) {
            $Websites = new Website();
            $Websites = $Websites->selectRaw('website_store_views.name as lang,website_stores.name as store_name,website_stores.id as website_stores_id,website_store_views.id as website_store_views_id,websites.id as website_table_id,website_stores.is_default as website_stores_default,website_stores.is_flag as website_stores_flag');
            $Websites = $Websites->join('website_stores', 'websites.id', '=', 'website_stores.website_id');
            $Websites = $Websites->join('website_store_views', 'website_store_views.website_store_id', '=', 'website_stores.id')
                ->where('website_store_views.code', $image->website_id)->where('websites.store_website_id', $image->store_website)->first();
            if ($Websites) {
                $image->lang = $Websites->lang;
                $image->store_name = $Websites->store_name;
                $image->website_stores_id = $Websites->website_stores_id;
                $image->website_store_views_id = $Websites->website_store_views_id;
                $image->website_table_id = $Websites->website_table_id;
                $image->website_stores_default = $Websites->website_stores_default;
                $image->website_stores_flag = $Websites->website_stores_flag;
            }

            $desktop = scraperImags::selectRaw('count(id) as desktop')->where('website_id', $image->website_id)
                ->where('store_website', $image->store_website)->where('device', 'desktop')->whereRaw('date(created_at) = "' . $image->date_created_at . '"')
                ->groupBy('website_id', 'store_website', DB::raw('date(created_at)'))->first();

            $mobile = scraperImags::selectRaw('count(id) as mobile')->where('website_id', $image->website_id)
                ->where('store_website', $image->store_website)->where('device', 'mobile')->whereRaw('date(created_at) = "' . $image->date_created_at . '"')
                ->groupBy('website_id', 'store_website', DB::raw('date(created_at)'))->first();

            $tablet = scraperImags::selectRaw('count(id) as tablet')->where('website_id', $image->website_id)
                ->where('store_website', $image->store_website)->where('device', 'tablet')->whereRaw('date(created_at) = "' . $image->date_created_at . '"')
                ->groupBy('website_id', 'store_website', DB::raw('date(created_at)'))->first();

            $image->desktop = isset($desktop->desktop) ? $desktop->desktop : 0;
            $image->mobile = isset($mobile->mobile) ? $mobile->mobile : 0;
            $image->tablet = isset($tablet->tablet) ? $tablet->tablet : 0;
        }

        $query = $request->search;

        $allWebsites = Website::pluck('name', 'id');

        $storewebsite = \App\StoreWebsite::pluck('title', 'id');
        $storewebsiteUrls = \App\StoreWebsite::pluck('website', 'id');

        $current_date = Carbon::now()->format('Y-m-d');

        $startDate = $current_date;
        $endDate = $current_date;

        return view('scrapper-phyhon.list', compact('images', 'allWebsites', 'request', 'query', 'storewebsite', 'current_date', 'startDate', 'endDate', 'storewebsiteUrls'));
    }

    public function listImages(Request $request)
    {
        $store_id = $request->id;

        $oldDate = null;
        $count = 0;
        $images = [];
        $website_id = 0;

        $categories = \App\SiteDevelopmentCategory::orderBy('title', 'asc')->get();
        $webStore = \App\WebsiteStore::where('id', $store_id)->first();

        if ($webStore) {
            $list = Website::where('id', $webStore->website_id)->first();
            $website_id = $list->id;

            $website_store_views = \App\WebsiteStoreView::where('website_store_id', $webStore->id)->first();

            if ($website_store_views) {
                $images = \App\scraperImags::where('store_website', $list->store_website_id)
                    ->where('website_id', $request->code); // this is language code. dont be confused with column name

                if (isset($request->startDate) && isset($request->endDate)) {
                    $images = $images->whereDate('created_at', '>=', date($request->startDate))
                        ->whereDate('created_at', '<=', date($request->endDate));
                } else {
                    //
                }

                if (isset($request->device) && ($request->device == 'mobile' || $request->device == 'tablet')) {
                    $images = $images->where('device', $request->device);
                } elseif ($request->device == 'desktop') {
                    $images = $images->orWhereNull('device')->whereNotIn('device', ['mobile', 'tablet']);
                }

                if (! empty($request->si_status)) {
                    if ($request->si_status == 1) {
                        $images = $images->where('si_status', 1);
                    } elseif ($request->si_status == 2) {
                        $images = $images->where('si_status', 2);
                    } elseif ($request->si_status == 3) {
                        $images = $images->where('si_status', 3);
                    } elseif ($request->si_status == 4) {
                        $images = $images->where('manually_approve_flag', 1);
                    } else {
                        $images = $images->where('si_status', 1);
                    }
                }

                $images = $images->paginate(Setting::get('pagination'));
            }
        }

        $allWebsites = Website::orderBy('name', 'ASC')->get();

        $allLanguages = Website::orderBy('name', 'ASC')->get();

        $startDate = $request->startDate;
        $endDate = $request->endDate;
        if ($request->ajax()) {
            $this->listImagesApprove($request);
            $view_path = 'scrapper-phyhon.list-image-products_ajax';
        } else {
            $view_path = 'scrapper-phyhon.list-image-products';
        }

        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->get();

        return view($view_path, compact('images', 'website_id', 'allWebsites', 'categories', 'startDate', 'endDate', 'bugTypes', 'bugEnvironments', 'bugSeveritys', 'bugStatuses', 'filterCategories', 'users', 'filterWebsites'));
    }

    public function listImagesApprove($requestData)
    {
        $store_id = $requestData->id;

        $requestData->page = ($requestData->page - 1);

        $oldDate = null;
        $count = 0;
        $images = [];
        $website_id = 0;

        $webStore = \App\WebsiteStore::where('id', $store_id)->first();

        if ($webStore) {
            $list = Website::where('id', $webStore->website_id)->first();
            $website_id = $list->id;

            $website_store_views = \App\WebsiteStoreView::where('website_store_id', $webStore->id)->first();

            if ($website_store_views) {
                $images = \App\scraperImags::where('store_website', $list->store_website_id)
                    ->where('website_id', $requestData->code); // this is language code. dont be confused with column name

                if (isset($requestData->startDate) && isset($requestData->endDate)) {
                    $images = $images->whereDate('created_at', '>=', date($requestData->startDate))
                        ->whereDate('created_at', '<=', date($requestData->endDate));
                } else {
                    //
                }

                if (isset($requestData->device) && ($requestData->device == 'mobile' || $requestData->device == 'tablet')) {
                    $images = $images->where('device', $requestData->device);
                } elseif ($requestData->device == 'desktop') {
                    $images = $images->orWhereNull('device')->whereNotIn('device', ['mobile', 'tablet']);
                }

                if (! empty($requestData->si_status)) {
                    if ($requestData->si_status == 1) {
                        $images = $images->where('si_status', 1);
                    } elseif ($requestData->si_status == 2) {
                        $images = $images->where('si_status', 2);
                    } elseif ($requestData->si_status == 3) {
                        $images = $images->where('si_status', 3);
                    } elseif ($requestData->si_status == 4) {
                        $images = $images->where('manually_approve_flag', 1);
                    } else {
                        $images = $images->where('si_status', 1);
                    }
                }

                $images->update(['si_status' => 2]);

                $images = $images->paginate(Setting::get('pagination'));
            }
        }
    }

    public function setDefaultStore(int $website = 0, int $store = 0, $checked = 0)
    {
        if ($website && $store) {
            try {
                if ($checked) {
                    WebsiteStore::where('website_id', $website)->update(['is_default' => 0]);
                }

                $store = WebsiteStore::find($store);

                $store->is_default = $checked;

                $store->save();

                $response = ['status' => 1, 'message' => 'The store state is changed.'];
            } catch (Exception $e) {
                $response = ['status' => 0, 'message' => $e->getMessage()];
            }

            return $response;
        }
    }

    public function setFlagStore(int $website = 0, int $store = 0, $checked = 0)
    {
        if ($website && $store) {
            try {
                if ($checked) {
                    WebsiteStore::where('is_flag', $website)->update(['is_flag' => 0]);
                }

                $store = WebsiteStore::find($store);

                $store->is_flag = $checked;

                $store->save();

                $response = ['status' => 1, 'message' => 'Url Flagged state has changed.'];
            } catch (Exception $e) {
                $response = ['status' => 0, 'message' => $e->getMessage()];
            }

            return $response;
        }
    }

    public function websiteStoreList(int $website = 0)
    {
        try {
            if ($website) {
                $stores = WebsiteStore::where('website_id', $website)->select('name', 'id')->get();

                $response = ['status' => 1, 'list' => $stores];
            }
        } catch (Exception $e) {
            $response = ['status' => 0, 'message' => $e->getMessage()];
        }

        return $response;
    }

    public function storeLanguageList(int $store = 0)
    {
        try {
            if ($store) {
                $language = \App\WebsiteStoreView::where('website_store_id', $store)->select('name', 'code', 'id')->get();

                $response = ['status' => 1, 'list' => $language];
            }
        } catch (Exception $e) {
            $response = ['status' => 0, 'message' => $e->getMessage()];
        }

        return $response;
    }

    public function imageSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'image' => 'required|valid_base',
            'image_name' => 'required',
            'store_website' => 'required|exists:store_websites,magento_url',
            'device' => 'required|in:desktop,mobile,tablet',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Invalid request',
                'error' => $validator->errors(),
            ]);
        }
        $StoreWebsite = \App\StoreWebsite::where('magento_url', $request->store_website)->first();

        $coordinates = $request->coordinates;

        if (is_array($coordinates)) {
            $coordinates = implode(',', $request->coordinates);
        } else {
            $coordinates = implode(',', json_decode($request->coordinates, true));
        }

        // For Height Width Of Base64
        $binary = \base64_decode(\explode(',', $request->image)[0]);
        $data = \getimagesizefromstring($binary);
        $width = $data[0];
        $height = $data[1];

        if ($this->saveBase64Image($request->image_name, $request->image)) {
            $image_parts = explode('_', $request->image_name);
            $image_date = $image_parts[2];

            $newImage = [
                'website_id' => $request->country_code,
                'store_website' => $StoreWebsite->id ?? 0,
                'img_name' => $request->image_name,
                'img_url' => $request->image_name,
                'device' => (isset($request->device) ? $request->device : 'desktop'),
                'coordinates' => $coordinates,
                'height' => $height,
                'width' => $width,
                'url' => $request->url,
                'scrap_date' => $image_parts[2],
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ];

            scraperImags::insert($newImage);

            return response()->json(['code' => 200, 'message' => 'Image successfully saved']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Something went wrong!']);
        }
    }

    public function saveBase64Image($file_name, $base64Image)
    {
        try {
            $base64Image = trim($base64Image);
            $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
            $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
            $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
            $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
            $base64Image = str_replace(' ', '+', $base64Image);
            $imageData = base64_decode($base64Image);

            // //Set image whole path here
            $filePath = public_path('scrappersImages') . '/' . $file_name;
            file_put_contents($filePath, $imageData);

            return true;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            \Log::error('scrapper_images :: ' . $th->getMessage());

            return false;
        }
    }

    public function callScrapper(Request $request)
    {
        $client = new Client();
        $res = null;
        $err = null;
        $store_website = \App\StoreWebsite::find($request->webName);
        $log_data = ['user_id' => \Auth::id(), 'action' => $request->data_name, 'website' => $request->webName, 'device' => $request->type, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
        try {
            $url = 'http://167.86.88.58:5000/' . $request->data_name;
            $data = [
                'type' => $request->type,
                'name' => $store_website->title,
            ];
            if ($request->webName != null && $request->is_flag != null) {
                $flagUrls = \App\scraperImags::where('store_website', $request->webName)->where('is_flaged_url', '1')->select('url')->get();
                $data['flagged'] = true;
                $count = 1;
                $fUrl = '';
                foreach ($flagUrls as $flagUrl) {
                    $fUrl .= $flagUrl['url'];
                    if ($count < count($flagUrls)) {
                        $fUrl .= ',';
                    }
                    $count++;
                }
                $data['urls'] = $fUrl;
            }
            $log_data['request'] = json_encode($data);
            $log_data['url'] = $url;

            $response = $client->post($url, [
                'json' => $data,
            ]);
            $res = $response->getBody()->getContents();
            $log_data['response'] = json_encode($res);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $err = $e->getResponse()->getBody()->getContents();
            $log_data['response'] = json_encode($err);
        }
        \Log::info($log_data);
        DB::table('scrapper_python_action_logs')->insert($log_data);

        return response()->json(['message' => $res, 'err' => $err]);
    }

    public function imageRemarkStore(Request $request)
    {
        $store_website = \App\Website::find($request->website_id);
        $cat_id = $request->cat_id;
        $remark = $request->remark;
        $site_development = SiteDevelopment::where('site_development_category_id', $cat_id)->where('website_id', $store_website->store_website_id)->orderBy('id', 'DESC');
        $sd = $site_development->first();
        if ($site_development->count() === 0) {
            $sd = new SiteDevelopment;
            $sd->site_development_category_id = $cat_id;
            $sd->website_id = $store_website->store_website_id;
            $sd->save();
        }

        $store_development_remarks = new \App\StoreDevelopmentRemark;
        $store_development_remarks->remarks = $remark;
        $store_development_remarks->store_development_id = $sd->id;
        $store_development_remarks->user_id = \Auth::id();
        $store_development_remarks->save();

        return response()->json(['message' => 'Remark Saved Successfully', 'remark' => $store_development_remarks, 'username' => \Auth::user()->name]);
    }

    public function changeCatRemarkList(Request $request)
    {
        $store_website = \App\Website::find($request->website_id);
        $site_development = SiteDevelopment::where('site_development_category_id', $request->remark)->where('website_id', $store_website->store_website_id)->get();
        $remarks = [];
        if (count($site_development) > 0) {
            foreach ($site_development as $val) {
                $sd_remarks = \App\StoreDevelopmentRemark::join('users as usr', 'usr.id', 'store_development_remarks.user_id')
                    ->where('store_development_remarks.store_development_id', $val->id)
                    ->select('store_development_remarks.*', 'usr.name as username')
                    ->get()->toArray();
                array_push($remarks, $sd_remarks);
            }
        }

        return response()->json(['remarks' => $remarks]);
    }

    public function history(Request $request)
    {
        $all_data = \App\scraperImags::join('store_websites', 'store_websites.id', 'scraper_imags.store_website')
            ->select('store_websites.website', 'scraper_imags.device', 'scraper_imags.created_at AS created_date', \DB::raw('count(`scraper_imags`.`id`) as no_image'));

        if (isset($request->startDate) && isset($request->endDate)) {
            $all_data = $all_data->whereDate('scraper_imags.created_at', '>=', date($request->startDate))
                ->whereDate('scraper_imags.created_at', '<=', date($request->endDate));
        } else {
            $all_data = $all_data->whereDate('scraper_imags.created_at', Carbon::now()->format('Y-m-d'));
        }

        $all_data = $all_data->orderBy('no_image', 'DESC')->groupBy('store_websites.website', 'scraper_imags.device')
            ->get();

        return response()->json(['history' => $all_data]);
    }

    public function actionHistory(Request $request)
    {
        $logs = DB::table('scrapper_python_action_logs')
            ->leftJoin('users', 'users.id', '=', 'scrapper_python_action_logs.user_id')
            ->select(['scrapper_python_action_logs.*', 'users.name']);
        if (isset($request->startDate) && isset($request->endDate)) {
            $logs = $logs->whereDate('scrapper_python_action_logs.created_at', '>=', date($request->startDate))
                ->whereDate('scrapper_python_action_logs.created_at', '<=', date($request->endDate));
        } else {
            //
        }

        $logs = $logs->orderBy('id', 'DESC')->get();
        $html = view('scrapper-phyhon.action_history', compact('logs'))->render();

        return ['message' => $html, 'statusCode' => 200];
    }

    public function delete(Request $request)
    {
        $images = scraperImags::whereDate('created_at', '=', date($request->delete_date))->get();

        foreach ($images as $image) {
            if (empty($image->img_name)) {
                continue;
            }

            $imagePath = public_path('scrappersImages/' . $image->img_name);

            if (file_exists($imagePath) && ! is_dir($imagePath)) {
                unlink($imagePath);
            }

            $image->delete();
        }

        return ['message' => count($images) . ' Deleted Successfully.', 'statusCode' => 200];
    }

    public function imageUrlList(Request $request)
    {
        $flagUrl = isset($request->flagUrl) ? $request->flagUrl : '';
        $storeWebsites = \App\StoreWebsite::get();
        if (isset($request->id)) {
            $store_id = $request->id;

            $urls = [];

            $webStore = \App\WebsiteStore::where('id', $store_id)->first();
            $list = Website::where('id', $webStore->website_id)->first();
            if ($webStore) {
                $website_store_views = \App\WebsiteStoreView::where('website_store_id', $webStore->id)->first();

                if ($website_store_views) {
                    $urls = \App\scraperImags::join('store_websites', 'store_websites.id', '=', 'scraper_imags.store_website')
                        ->select('scraper_imags.*', 'store_websites.title as wtitle', 'store_websites.id as swid')
                        ->where('store_website', $list->store_website_id)
                        ->where('website_id', $request->code) // this is language code. dont be confused with column name
                        ->whereRaw('url != "" and url IS  NOT NULL');
                    if (isset($request->startDate) && isset($request->endDate)) {
                        $urls = $urls->whereDate('created_at', '>=', date($request->startDate))
                            ->whereDate('created_at', '<=', date($request->endDate));
                    } else {
                        //
                    }

                    if ($request->flt_website && $request->flt_website != null) {
                        $urls = $urls->where('store_website', $request->flt_website);
                    }
                    if ($request->scrapper_url && $request->scrapper_url != null) {
                        $urls->where('url', 'LIKE', '%' . $request->scrapper_url . '%');
                    }

                    $urls = $urls->paginate(Setting::get('pagination'));
                }
            }
        } else {
            $urls = DB::table('scraper_imags')->join('store_websites', 'store_websites.id', '=', 'scraper_imags.store_website')->select('scraper_imags.*', 'store_websites.title as wtitle', 'store_websites.id as swid')->whereRaw('url != "" and url IS  NOT NULL');
            if (! empty($flagUrl)) {
                $urls = $urls->where('scraper_imags.id', $flagUrl);
                $flagUrl = '#' . $flagUrl;
            }
            if ($request->flt_website && $request->flt_website != null) {
                $urls = $urls->where('store_website', $request->flt_website);
            }

            if ($request->scrapper_url && $request->scrapper_url != null) {
                $urls->where('url', 'LIKE', '%' . $request->scrapper_url . '%');
            }
            $urls = $urls->paginate(Setting::get('pagination'));
        }

        return view('scrapper-phyhon.list_urls', compact('urls', 'flagUrl', 'storeWebsites'));
    }

    public function flagImageUrl($id)
    {
        $image = \App\scraperImags::find($id);
        $image->is_flaged_url = ($image->is_flaged_url == 1) ? 0 : 1;
        $status = ($image->is_flaged_url == 1) ? 'Flagged' : 'un-flagged';
        $image->save();

        return redirect()->back()
            ->with('success', "Url $status successfully");
    }

    public function rejectScrapperImage(Request $request)
    {
        $image = \App\scraperImags::find($request->id);
        $image->si_status = $request->si_status;
        $image->manually_approve_flag = 0;
        $image->save();

        return redirect()->back()->with('success', 'Scrapper image has been successfully rejected.');
    }
}
