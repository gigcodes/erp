<?php

declare(strict_types=1);

namespace App\Http\Controllers\Zabbix;

use App\Zabbix\ZabbixException;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Zabbix\Item;

class ItemController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $item = new Item();

        $items = $item->getAllItems();

        return view('zabbix.item.index', [
            'items' => $items
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $data = $request->all();

        try {
            $item = new Item();
            $itemId = (int)$data['id'] ?? null;
            if (!empty($data['id'])) {
                $item = $item->getById($itemId);
            }

            $item->setKey($data['key'] ?? '');
            $item->setName($data['name'] ?? '');
            $item->setType($data['type'] ?? '');
            $item->setValueType((int)$data['value_type'] ?? 1);
            $item->setDelay($data['delay'] ?? 1);

            $item->save();
        }
        catch (ZabbixException $zabbixException)
        {
            return response()->json([
                'message' => $zabbixException->getMessage(),
                'code' => 500
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'code' => 500
            ]);
        }

        return response()->json([
            'message' => sprintf('Item with name: %s was edited. Reload page.', $item->getName()),
            'item' => $item,
            'code' => 200
        ]);
    }
}