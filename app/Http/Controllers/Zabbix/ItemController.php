<?php

declare(strict_types=1);

namespace App\Http\Controllers\Zabbix;

use App\Models\Zabbix\Trigger;
use App\Zabbix\ZabbixApi;
use App\Zabbix\ZabbixException;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Zabbix\Item;

class ItemController extends Controller
{

    public function __construct(
        private Trigger $trigger,
        private ZabbixApi $zabbix
    ) {
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request, $hostId = null)
    {
        $item = new Item();

        if ($hostId) {
            $items = $item->getItemsByHostId($hostId);
        } else {
            $items = $item->getAllItems();
        }

        $templates = $this->trigger->getAllTemplates();

        return view('zabbix.item.index', [
            'items' => $items,
            'templates' => $templates
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
            $item->setHostId((int)$data['host_id']);
            $item->setUnits($data['units'] ?? '');
            $item->setInterfaceid((int)$data['interfaceid'] ?? 0);

            if (!empty($data['template_id'])) {
                $item->setTemplateId((int)$data['template_id']);
            }

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

    public function delete(Request $request)
    {
        $data = $request->all();

        try {
            $user = new Item();
            $userId = (int)$data['id'] ?? null;
            if (!empty($data['id'])) {
                $user = $user->getById($userId);
            } else {
                throw new ZabbixException(sprintf('Item with id: %s not found.', $userId));
            }

            $user->delete();
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
            'message' => sprintf('Item with id: %s was deleted. Reload page.', $userId),
            'code' => 200
        ]);
    }
}