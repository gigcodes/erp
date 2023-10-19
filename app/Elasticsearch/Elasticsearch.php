<?php

declare(strict_types=1);

namespace App\Elasticsearch;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;

class Elasticsearch
{
    protected Client $connection;

    public function __construct()
    {
        $hosts = explode(',', env('ELASTICSEARCH_HOST'));
        $this->connection = ClientBuilder::create()
            ->setHosts(
                $hosts ?? [$_ENV['ELASTICSEARCH_HOST'] ?? 'elasticsearch:9200']
            )
            ->build();
    }

    public function getConn(): Client
    {
        return $this->connection;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return (new self())->getConn()->$name(...$arguments);
    }
}