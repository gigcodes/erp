<?php

declare(strict_types=1);

namespace App\Models\Zabbix;

use App\Zabbix\Zabbix;
use JsonSerializable;

class Item implements JsonSerializable
{
    /**
     * @var Zabbix
     */
    private $zabbix;
    /**
     * @var
     */
    private $id;
    /**
     * @var
     */
    private $name;
    /**
     * @var
     */
    private $type;
    /**
     * @var
     */
    private $key;
    /**
     * @var
     */
    private $valueType;
    private $interfaceId;
    private $hostId;
    /**
     * @var
     */
    private $delay;

    public function __construct()
    {
        $this->zabbix = new Zabbix();
    }

    /**
     * @return Item[]
     */
    public function getAllItems()
    {
        return array_map(function ($item) {
            $model = new self();
            $model->setName($item['name'] ?? '');
            $model->setType($item['type'] ?? '');
            $model->setId((int)$item['itemid'] ?? null);
            $model->setValueType((int)$item['value_type'] ?? 0);
            $model->setKey((string)$item['key_'] ?? '');
            $model->setDelay((string)$item['delay'] ?? '');
            $model->setInterfaceid((int)$item['interfaceid'] ?? 0);
            $model->setHostId((int)$item['hostid'] ?? 0);
            return $model;
        }, $this->zabbix->getAllItems());
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return (int)$this->id;
    }

    /**
     * @param int|null $id
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return (string)$this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return (string)$this->key;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDelay(): ?string
    {
        return (string)$this->delay;
    }

    /**
     * @param int $delay
     * @return $this
     */
    public function setDelay(string $delay): self
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getValueType(): ?int
    {
        return (int)$this->valueType;
    }

    /**
     * @param int|null $valueType
     * @return $this
     */
    public function setValueType(?int $valueType): self
    {
        $this->valueType = $valueType;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInterfaceid(): ?int
    {
        return (int)$this->interfaceId;
    }

    /**
     * @param int|null $interfaceId
     * @return $this
     */
    public function setInterfaceid(?int $interfaceId): self
    {
        $this->interfaceId = $interfaceId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHostId(): ?int
    {
        return (int)$this->hostId;
    }

    /**
     * @param int|null $hostId
     * @return $this
     */
    public function setHostId(?int $hostId): self
    {
        $this->hostId = $hostId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return (string)$this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function save()
    {
        if (!$this->getId()) {
            $this->zabbix->saveUser([
                'name' => $this->getName(),
                'key_' => $this->getKey(),
                'hostid' => $this->getHostId(),
                'type' => $this->getType(),
                'value_type' => $this->getValueType(),
                'interfaceid' => $this->getInterfaceid(),
                'delay' => $this->getDelay(),
            ]);
            return $this->getById(1);
        } else {
            $this->zabbix->updateUser([
                'name' => $this->getName(),
                'key_' => $this->getKey(),
                'hostid' => $this->getHostId(),
                'type' => $this->getType(),
                'value_type' => $this->getValueType(),
                'interfaceid' => $this->getInterfaceid(),
                'delay' => $this->getDelay()
            ]);
            return $this->getById($this->getId());
        }
    }

    public function getById(int $id): ?self
    {
        $item = $this->zabbix->getUserByIds($id);

        if (!$item) {
            return null;
        }

        return (new self())->setData($item);
    }

    public function setData(array $data = [])
    {
        $this->setName($data['name'] ?? '');
        $this->setType($data['type'] ?? '');
        $this->setId((int)$data['itemid'] ?? null);
        $this->setDelay($data['delay'] ?? '');
        $this->setValueType((int)$data['value_type'] ?? null);
        $this->setKey((string)$data['key_'] ?? '');
        $this->setHostId((int)$data['hostid'] ?? 0);
        $this->setInterfaceid((int)$data['interfaceid'] ?? 0);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'key' => $this->getKey(),
            'type' => $this->getType(),
            'value_type' => $this->getValueType(),
            'intarfaceid' => $this->getInterfaceid(),
            'delay' => $this->getDelay(),
            'host_id' => $this->getHostId(),
        ];
    }
}