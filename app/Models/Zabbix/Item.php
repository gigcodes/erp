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
    private $units;
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
            $model->setData($item);
            return $model;
        }, $this->zabbix->getAllItems());
    }

    /**
     * @return Item[]
     */
    public function getItemsByHostId(int $hostId)
    {
        return array_map(function ($item) {
            $model = new self();
            $model->setData($item);
            return $model;
        }, $this->zabbix->getAllItemsByHostId($hostId));
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

    /**
     * @return string|null
     */
    public function getUnits(): ?string
    {
        return (string)$this->units;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setUnits($units): self
    {
        $this->units = $units;

        return $this;
    }

    public function save()
    {
        if (!$this->getId()) {
            $this->zabbix->saveItem([
                'name' => $this->getName(),
                'key_' => $this->getKey(),
                'hostid' => $this->getHostId(),
                'type' => $this->getType(),
                'value_type' => $this->getValueType(),
                'interfaceid' => $this->getInterfaceid(),
                'delay' => $this->getDelay(),
                'units' => $this->getUnits()
            ]);
        } else {
            $this->zabbix->updateItem([
                'name' => $this->getName(),
                'key_' => $this->getKey(),
                'hostid' => $this->getHostId(),
                'type' => $this->getType(),
                'value_type' => $this->getValueType(),
                'interfaceid' => $this->getInterfaceid(),
                'delay' => $this->getDelay(),
                'units' => $this->getUnits()
            ]);
        }
    }

    public function getById(int $id): ?self
    {
        $item = $this->zabbix->getItemByIds($id);

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
        $this->setValueType((int)$data['value_type'] ?? 0);
        $this->setKey((string)$data['key_'] ?? '');
        $this->setDelay((string)$data['delay'] ?? '');
        $this->setInterfaceid((int)$data['interfaceid'] ?? 0);
        $this->setHostId((int)$data['hostid'] ?? 0);
        $this->setUnits($data['units'] ?? 0);

        return $this;
    }

    public function delete(): ?int
    {
        $this->zabbix->deleteItem($this->getId());

        return $this->getId();
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
            'units' => $this->getUnits()
        ];
    }
}