<?php

declare(strict_types=1);

namespace App\Http\Controllers\PageBuilder;

use Exception;
use Throwable;
use App\Models\MagentoPageBuilder as PageBuilder;
use App\StoreWebsite;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MagentoPageBuilderController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $pages = PageBuilder::query()->orderBy('id', 'desc')->get();
        $adminPath = PageBuilder::getPath();
        return view('magento_pagebuilder.index', [
            'pages' => $pages,
            'admin_path' => $adminPath
        ]);
    }

    public function createOrEdit(Request $request)
    {
        $data = $request->all();
        $action = 'create';

        try {
            if (!empty($data['page_id'])) {
                $pageBuilder = PageBuilder::find($data['page_id']);
                $action = 'edit';
            }
            else {
                $pageBuilder = new PageBuilder();
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