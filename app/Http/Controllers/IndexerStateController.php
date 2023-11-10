<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ChatMessage;
use App\Elasticsearch\Elasticsearch;
use App\Models\IndexerState;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Elasticsearch\Reindex\Interfaces\Reindex;
use Illuminate\Support\Facades\Artisan;

class IndexerStateController extends Controller
{
    public function index(Request $request)
    {
        $this->createIndexerStateIfNotExist();
        $indexerStates = IndexerState::all();

        if ($request->ajax()) {
            $view = (string)view('indexer_state.list', [
                'indexerStates' => $indexerStates
            ]);

            return response()->json(['code' => 200, 'tpl' => $view]);
        }

        return view('indexer_state.index', [
            'indexerStates' => $indexerStates
        ]);
    }

    public function elasticConnect()
    {
        try {
            $elastic = new Elasticsearch();
            $elastic->connect();
            $elastic->getConn()->ping();
            return response()->json([
                'code' => 200,
                'message' => 'Connection successful to elasticsearch.'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reindex(Request $request)
    {
        try {
            $data = $request->all();

            $id = $data['id'] ?? null;

            if ($id === null) {
                throw new \Exception('Id is required param.');
            }

            /** @var IndexerState $indexerState */
            $indexerState = IndexerState::find($id);

            if ($indexerState === null) {
                throw new \Exception(sprintf('Indexer with %s id not found.', $id));
            }

            if ($indexerState->isSkip()) {
                throw new \Exception(sprintf('Cannot start again reindex for index: %s', $indexerState->getIndex()));
            }

            Artisan::call('reindex:messages');
        } catch (\Throwable $throwable) {
            return response()->json(['message' => $throwable->getMessage(), 'code' => 500], 500);
        }
        return response()->json(['message' => 'Reindex started.', 'code' => 200]);
    }

    public function save(Request $request)
    {
        try {
            $data = $request->all();

            $id = $data['id'] ?? null;

            if ($id === null) {
                throw new \Exception('Id is required param.');
            }

            /** @var IndexerState $indexerState */
            $indexerState = IndexerState::find($id);

            if ($indexerState === null) {
                throw new \Exception(sprintf('Indexer with %s id not found.', $id));
            }

            if ($data['cycles']) {
                $indexerState->setSettings([
                    'cycles' => (int)$data['cycles']
                ]);
                $indexerState->save();
            }

            return response()->json(['message' => 'Indexer saved.', 'code' => 200]);
        }
        catch (\Throwable $throwable) {
            return response()->json(['message' => $throwable->getMessage(), 'code' => 500], 500);
        }
    }

    public function masterSlave(Request $request)
    {
        $select = ChatMessage::query()->limit(5)->orderBy('id', 'DESC');
        $array = json_decode('{"id":88550,"is_queue":0,"unique_id":null,"number":null,"message":"#DEVTASK-1001-SOLO LUXURY - Live Chat=>Check this PR: http:\/\/erpstage.theluxuryunlimited.com\/logging\/live-laravel-logs and give me know","lead_id":null,"order_id":null,"order_status":null,"customer_id":null,"purchase_id":null,"supplier_id":null,"vendor_id":null,"charity_id":null,"user_id":4,"sop_user_id":null,"sent_to_user_id":2,"ticket_id":null,"send_to_tickets":null,"task_id":null,"account_id":null,"instagram_user_id":null,"lawyer_id":null,"case_id":null,"blogger_id":null,"voucher_id":null,"developer_task_id":1001,"bug_id":null,"test_case_id":null,"issue_id":1001,"erp_user":2,"contact_id":null,"dubbizle_id":null,"site_development_id":null,"payment_receipt_id":null,"test_suites_id":null,"assigned_to":null,"created_at":"2023-11-07T10:10:07.000000Z","updated_at":"2023-11-10T04:36:41.000000Z","approved":true,"status":2,"sent":0,"is_delivered":0,"is_read":0,"error_status":1,"error_info":"{\"number\":null,\"error\":\"Empty reply from server\"}","resent":0,"is_reminder":0,"is_audio":0,"is_email":0,"from_email":null,"to_email":null,"cc_email":null,"media_url":null,"is_processed_for_keyword":0,"document_id":0,"group_id":null,"old_id":null,"is_chatbot":0,"message_application_id":0,"social_strategy_id":null,"store_social_content_id":null,"quoted_message_id":null,"hubstaff_activity_summary_id":null,"time_doctor_activity_summary_id":"","question_id":null,"learning_id":null,"additional_data":null,"hubstuff_activity_user_id":null,"time_doctor_activity_user_id":"","user_feedback_id":null,"user_feedback_category_id":null,"user_feedback_status":null,"send_by":null,"email_id":null,"message_en":null,"scheduled_at":null,"broadcast_numbers_id":null,"task_time_reminder":0,"flow_exit":0,"ui_check_id":null,"send_by_simulator":0,"message_type":null}', true);
        unset($array['id']);
        $create = ChatMessage::create($array);

        return response()->json(
            [
                'data' => [
                    'select' => [
                        'host' => $select->getConnection()->getConfig('host'),
                        'data' => $select->get(),
                    ],
                    'insert' => [
                        'host' => $create->getConnection()->getConfig('host'),
                        'data' => $create,
                    ]
                ]
            ]
        );
    }

    private function createIndexerStateIfNotExist(): void
    {
        foreach (IndexerState::INDEXER_MAPPING as $index => $className) {
            $exists = IndexerState::where(IndexerState::INDEX, $index)->exists();

            if (!$exists) {
                $indexerState = new IndexerState();
                $indexerState->setIndex($index);
                $indexerState->setStatus(Reindex::INVALIDATE);
                $indexerState->save();
            }
        }
    }
}