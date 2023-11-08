<?php

declare(strict_types=1);

namespace App\Elasticsearch\Reindex\Interfaces;

use App\Models\IndexerState;

interface Reindex
{
    const RUNNING = 'running';
    const INVALIDATE = 'invalidate';
    const VALID = 'valid';

    /**
     * @return void
     */
    public function execute(array $params = []): void;

    /**
     * @return array
     */
    public function configure(): array;

    /**
     * @param IndexerState $indexerState
     * @return self
     */
    public function setIndexerState(IndexerState $indexerState): self;

    /**
     * @return IndexerState
     */
    public function getIndexerState(): IndexerState;
}