<?php

declare(strict_types=1);

namespace App\Elasticsearch\Reindex;

use App\ChatMessage;
use App\Elasticsearch\Elasticsearch;
use App\Elasticsearch\Reindex\Interfaces\Reindex;
use Illuminate\Support\Facades\DB;

class Messages implements Reindex
{
    const INDEX_NAME = 'chatbot_messages';
    const LIMIT = 5000;

    /**
     * @inheritDoc
     */
    public function execute(array $params = []): void
    {
        for ($page = 1;$page<=500;$page++) {
            $messages = $this->getMessagesFromDB($page);
            if (!$messages) {
                break;
            }
            $this->reindex($messages);
            unset($messages);
            $page++;
        }
    }

    /**
     * @inheritDoc
     */
    public function configure(): array
    {
        return [
            'index' => self::INDEX_NAME
        ];
    }

    private function getMessagesFromDB(int $page = 1)
    {
        $pendingApprovalMsg = ChatMessage::with('taskUser', 'chatBotReplychat', 'chatBotReplychatlatest')
            ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'c.store_website_id')
            ->leftJoin('bug_trackers  as bt', 'bt.id', 'chat_messages.bug_id')
            ->leftJoin('chatbot_replies as cr', 'cr.replied_chat_id', 'chat_messages.id')
            ->leftJoin('chat_messages as cm1', 'cm1.id', 'cr.chat_id')
            ->leftJoin('emails as e', 'e.id', 'chat_messages.email_id')
            ->leftJoin('tmp_replies as tmp', 'tmp.chat_message_id', 'chat_messages.id')
            ->groupBy(['chat_messages.customer_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.email_id']);

        $pendingApprovalMsg = $pendingApprovalMsg->whereRaw('chat_messages.id in (select max(chat_messages.id) as latest_message from chat_messages LEFT JOIN chatbot_replies as cr on cr.replied_chat_id = `chat_messages`.`id` where ((customer_id > 0 or vendor_id > 0 or task_id > 0 or developer_task_id > 0 or user_id > 0 or supplier_id > 0 or bug_id > 0 or email_id > 0) OR (customer_id IS NULL
        AND vendor_id IS NULL
        AND supplier_id IS NULL
        AND bug_id IS NULL
        AND task_id IS NULL
        AND developer_task_id IS NULL
        AND email_id IS NULL
        AND user_id IS NULL)) GROUP BY customer_id,user_id,vendor_id,supplier_id,task_id,developer_task_id, bug_id,email_id)');

        $select = ['cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'chat_messages.*', 'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer', 'cm1.is_audio as answer_is_audio', 'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name',
            'tmp.id as tmp_replies_id', 'tmp.suggested_replay', 'tmp.is_approved', 'tmp.is_reject', 'c.is_auto_simulator as customer_auto_simulator',
            'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator', DB::raw('CASE WHEN `e`.`id` IS NOT NULL THEN 1 ELSE 0 END AS is_email')];
        $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
            $q->where('chat_messages.message', '!=', '');
        })->select($select)
            ->orderByRaw('cr.id DESC, chat_messages.id DESC')
            ->offset(($page - 1) * self::LIMIT)->limit(self::LIMIT);

        return (array)$pendingApprovalMsg->get()->getIterator();
    }

    public function reindex($messages = [])
    {
        foreach ($messages as $item) {
            Elasticsearch::index([
                'index' => self::INDEX_NAME,
                'body' => $item->getAttributes(),
            ]);
        }
    }

    private function removeAll()
    {
        Elasticsearch::deleteByQuery([
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'match_all' => (object)[]
                ]
            ]]);
    }

    public function indexOne(ChatMessage $chatMessage): void
    {
        $pendingApprovalMsg = ChatMessage::with('taskUser', 'chatBotReplychat', 'chatBotReplychatlatest')
            ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'c.store_website_id')
            ->leftJoin('bug_trackers  as bt', 'bt.id', 'chat_messages.bug_id')
            ->leftJoin('chatbot_replies as cr', 'cr.replied_chat_id', 'chat_messages.id')
            ->leftJoin('chat_messages as cm1', 'cm1.id', 'cr.chat_id')
            ->leftJoin('emails as e', 'e.id', 'chat_messages.email_id')
            ->leftJoin('tmp_replies as tmp', 'tmp.chat_message_id', 'chat_messages.id')
            ->groupBy(['chat_messages.customer_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.email_id']);

        $pendingApprovalMsg = $pendingApprovalMsg->whereRaw('chat_messages.id in (select max(chat_messages.id) as latest_message from chat_messages LEFT JOIN chatbot_replies as cr on cr.replied_chat_id = `chat_messages`.`id` where ((customer_id > 0 or vendor_id > 0 or task_id > 0 or developer_task_id > 0 or user_id > 0 or supplier_id > 0 or bug_id > 0 or email_id > 0) OR (customer_id IS NULL
        AND vendor_id IS NULL
        AND supplier_id IS NULL
        AND bug_id IS NULL
        AND task_id IS NULL
        AND developer_task_id IS NULL
        AND email_id IS NULL
        AND user_id IS NULL)) GROUP BY customer_id,user_id,vendor_id,supplier_id,task_id,developer_task_id, bug_id,email_id)');

        $select = ['cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'chat_messages.*', 'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer', 'cm1.is_audio as answer_is_audio', 'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name',
            'tmp.id as tmp_replies_id', 'tmp.suggested_replay', 'tmp.is_approved', 'tmp.is_reject', 'c.is_auto_simulator as customer_auto_simulator',
            'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator', DB::raw('CASE WHEN `e`.`id` IS NOT NULL THEN 1 ELSE 0 END AS is_email')];
        $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
            $q->where('chat_messages.message', '!=', '');
        })->select($select);
        $pendingApprovalMsg->where('chat_messages.id', $chatMessage->id);

        $model = $pendingApprovalMsg->first();

        if (!$model) {
            return;
        }

        $this->save($model);
    }

    public function getDocumentById($id) {
        try {
            if ($id === null) {
                return null;
            }
            $record = Elasticsearch::search(
                [
                    'index' => self::INDEX_NAME,
                    'body' => [
                        'query' => [
                            'match' => ['id' => $id]
                        ]
                    ]
                ]);

            if ($record === null) {
                return null;
            }

            $record = $record['hits']['hits'];

            if (!$record) {
                return null;
            }

            $model = new ChatMessage();
            $model->setRawAttributes($record[0]['_source']);
            $model->setAttribute('_id', $record[0]['_id']);

            return $model;
        }
        catch (\Exception $e) {
            return null;
        }
    }

    public function updateDocumentById($_id, $model) {
        $params = [
            'index' => self::INDEX_NAME,
            'id' => $_id,
            'body' => [
                'doc' => $model->getAttributes()
            ]
        ];
        Elasticsearch::update($params);

        return true;
    }

    public function save($model)
    {
        try {
            $elastic = $this->getDocumentById($model->getAttribute('id'));

            if ($elastic !== null) {
                $this->updateDocumentById($elastic->getAttribute('_id'), $model);
            } else {
                Elasticsearch::index([
                    'index' => self::INDEX_NAME,
                    'body' => $model->getAttributes(),
                ]);
            }
        }
        catch (\Exception $e) {
            return $model;
        }
    }
}