<?php

declare(strict_types=1);

namespace App\Http\Controllers\PageBuilder;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use App\Models\MagentoPageBuilder as PageBuilder;
use App\StoreWebsite;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class MagentoPageBuilderController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request, int $storeId = null)
    {
        StoreWebsite::where('id', $storeId);

        try {
            $store = StoreWebsite::where('id', $storeId)->first();

            if ($store === null) {
                throw new ModelNotFoundException(sprintf('Magento store with id: %s not found.', $storeId));
            }
        }
        catch (ModelNotFoundException $foundException)
        {
            return response()->json(['message' => $foundException->getMessage()], 404);
        }
        catch (Exception $e) {

        }

        $pages = PageBuilder::where('magento_store_id', $store->id)->orderBy('id', 'desc')->get();

        return view('magento_pagebuilder.index', [
            'pages' => $pages,
            'store' => $store,
        ]);
    }

    public function createOrEdit(Request $request)
    {
        $data = $request->all();

        try {
            if (!empty($data['page_id'])) {
                $pageBuilder = PageBuilder::where('title', 'like', '%'.$data['title'] ?? ''.'%')->first();
                $data['update_time'] = Carbon::now()->toDateTimeString();

                if ($pageBuilder === null) {
                    unset($data['update_time']);
                    $pageBuilder = new PageBuilder();
                }
            }
            else {
                $pageBuilder = new PageBuilder();
                $data['creation_time'] = Carbon::now()->toDateTimeString();
            }

            $columns = $pageBuilder->getColumns();
            foreach ($data as $key => $value) {
                if (in_array($key, ['id', 'page_id'])) {
                    continue;
                }
                if (!in_array($key, $columns)) {
                    continue;
                }
                $pageBuilder->$key = $value;
            }

            $magentoDomain = $data['magento_domain'] ?? '';

            $store = StoreWebsite::where('magento_url', 'like', '%' . $magentoDomain . '%')->first();

            if ($store !== null) {
                $pageBuilder->magento_store_id = $store->id;
            } else {
                throw new Exception(sprintf('Store with host: %s not found.', $magentoDomain));
            }

            $pageBuilder->save();
        }
        catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => sprintf('Could\'nt save page. Message: ', $e->getMessage())
            ], 500);
        }

        return response()->json([
            'code' => 200,
            'message' => sprintf('Page was saved on erp with id: %s. Please reload the page on ERP.', $pageBuilder->id)
        ]);
    }

    public function pageById(Request $request, int $pageId)
    {
        $page = PageBuilder::find($pageId);
        return response()->json([
            'data' => $page
        ]);
    }
}