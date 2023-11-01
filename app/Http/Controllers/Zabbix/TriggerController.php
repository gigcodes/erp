<?php

declare(strict_types=1);

namespace App\Http\Controllers\Zabbix;

use App\Zabbix\ZabbixException;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Zabbix\Trigger;

class TriggerController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $trigger = new Trigger();

        $triggers = array_reverse($trigger->getAll());
        $count = sizeof($triggers);
        $triggers = array_slice($triggers, $page * 50, 50);
        $templates = $trigger->getAllTemplates();

        if ($request->ajax()) {
            $view = (string)view('zabbix.trigger.list', [
                'triggers' => $triggers,
                'page' => $page,
            ]);

            return response()->json([
                'tpl' => $view,
                'code' => 200
            ]);
        }

        return view('zabbix.trigger.index', [
            'triggers' => $triggers,
            'count' => $count,
            'page' => $page,
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
            $item = new Trigger();
            $itemId = (int)$data['id'] ?? null;
            if (!empty($data['id'])) {
                $item = $item->getById($itemId);
            }

            $item->setEventName($data['event_name'] ?? '');
            $item->setName($data['name'] ?? '');
            $item->setExpression($data['expression'] ?? '');
            if (!empty($data['template_id']) && $data['template_id'] !== 0) {
                $item->setTemplateId((int)$data['template_id'] ?? 0);
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
}