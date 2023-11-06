<?php

declare(strict_types=1);

namespace App\Elasticsearch\Reindex\Interfaces;

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
}