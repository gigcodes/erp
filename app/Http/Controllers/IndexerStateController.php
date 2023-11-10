<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ChatMessage;
use App\Elasticsearch\Elasticsearch;
use App\Models\IndexerState;
use App\User;
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
        $select = User::query()->limit(5)->orderBy('id', 'DESC');
        $rand = rand(1,55);
        $create = User::create(['name'=>'test'.$rand, 'email'=>"test".$rand."@example.com", 'password' => '$2y$10$Sr8Gzf8en1WuxAl0XRB1se3loslJH/kIOt3Dyz6zZ4eqYEae9J5Uq']);
        return response()->json(
            [
                'data' => [
                    'select' => [
                        'host' => $select->getConnection()->getConfig('host')
                    ],
                    'insert' => [
                        'host' => $create->getConnection()->getConfig('host')
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