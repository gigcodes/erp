<?php

declare(strict_types=1);

namespace App\Models;

use App\Elasticsearch\Reindex\Messages;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Elasticsearch\Reindex\Interfaces\Reindex;

class IndexerState extends Model
{
    use HasFactory;

    const ID = 'id';
    const INDEX = 'index';
    const STATUS = 'status';
    const SETTINGS = 'settings';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';
    const LOGS = 'logs';

    const INDEXER_MAPPING = [
        Messages::INDEX_NAME => Messages::class
    ];

    protected $table = 'indexer_state';

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return (int)$this->getAttribute('id');
    }

    /**
     * @return string|null
     */
    public function getIndex(): ?string
    {
        return (string)$this->getAttribute(self::INDEX);
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return (string)$this->getAttribute(self::STATUS);
    }

    /**
     * @return array|null
     */
    public function getSettings(): ?array
    {
        return json_decode($this->getAttribute(self::SETTINGS), true);
    }

    /**
     * @return mixed
     */
    public function getLogs(): mixed
    {
        return json_decode($this->getAttribute(self::LOGS), true);
    }

    /**
     * @return Carbon|null
     */
    public function getUpdatedAt(): ?Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    /**
     * @return Carbon|null
     */
    public function getCreatedAt(): ?Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    /**
     * @param string $index
     * @return $this
     */
    public function setIndex(string $index): self
    {
        $this->setAttribute(self::INDEX, $index);

        return $this;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->setAttribute(self::STATUS, $status);

        return $this;
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function setSettings(array $settings = []): self
    {
        $this->setAttribute(self::SETTINGS, json_encode($settings));

        return $this;
    }

    /**
     * @param array $logs
     * @return $this
     */
    public function setLogs(array $logs = []): self
    {
        $this->setAttribute(self::LOGS, json_encode($logs));

        return $this;
    }

    /**
     * skip if running
     * @return bool
     */
    public function isSkip(): bool
    {
        if ($this->getStatus() === Reindex::RUNNING) {
            return true;
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function getClassName(): ?string
    {
        return self::INDEXER_MAPPING[$this->getIndex()];
    }
}