<?php

declare(strict_types=1);

namespace App\Models\Zabbix;

use App\Zabbix\Zabbix;
use JsonSerializable;

class User implements JsonSerializable
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
    private $surname;
    /**
     * @var
     */
    private $roleId;
    /**
     * @var
     */
    private $url;
    /**
     * @var
     */
    private $timezone;
    /**
     * @var
     */
    private $autologin;
    private $username;
    private $password;

    public function __construct()
    {
        $this->zabbix = new Zabbix();
    }

    /**
     * @return User[]
     */
    public function getAllUsers()
    {
        return array_map(function ($item) {
            $model = new self();
            $model->setName($item['name'] ?? '');
            $model->setUsername($item['username'] ?? '');
            $model->setId((int)$item['userid'] ?? null);
            $model->setSurname($item['surname'] ?? '');
            $model->setRoleId((int)$item['roleid'] ?? null);
            $model->setUrl((string)$item['url'] ?? '');
            $model->setTimezone((string)$item['timezone'] ?? '');
            $model->setAutologin((int)$item['autologin'] ?? 0);
            return $model;
        }, $this->zabbix->getAllUsers());
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
    public function getUsername(): ?string
    {
        return (string)$this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return (string)$this->surname;
    }

    /**
     * @param string $surname
     * @return $this
     */
    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRoleId(): ?int
    {
        return (int)$this->roleId;
    }

    /**
     * @param int|null $roleId
     * @return $this
     */
    public function setRoleId(?int $roleId): self
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return (string)$this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return (string)$this->timezone;
    }

    /**
     * @param string $timezone
     * @return $this
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return (string)$this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAutologin(): ?int
    {
        return (int)$this->autologin;
    }

    /**
     * @param int|null $autologin
     * @return $this
     */
    public function setAutologin(?int $autologin): self
    {
        $this->autologin = $autologin;

        return $this;
    }

    public function save()
    {
        if (!$this->getId()) {
            $this->zabbix->saveUser([
                'username' => $this->getUsername(),
                'name' => $this->getName(),
                'surname' => $this->getSurname(),
                'roleid' => $this->getRoleId(),
                'passwd' => $this->getPassword(),
                'url' => $this->getUrl(),
                'usrgrps' => [
                    [
                        'usrgrpid' => 7
                    ]
                ]
            ]);
            return $this->getById(1);
        } else {
            $this->zabbix->updateUser([
                'name' => $this->getName(),
                'surname' => $this->getSurname(),
                'roleid' => $this->getRoleId(),
                'userid' => $this->getId(),
                'url' => $this->getUrl(),
            ]);
            return $this->getById($this->getId());
        }
    }

    public function getById(int $id): ?self
    {
        $user = $this->zabbix->getUserByIds($id);

        if (!$user) {
            return null;
        }

        return (new self())->setData($user);
    }

    public function setData(array $data = [])
    {
        $this->setName($data['name'] ?? '');
        $this->setUsername($data['username'] ?? '');
        $this->setId((int)$data['userid'] ?? null);
        $this->setSurname($data['surname'] ?? '');
        $this->setRoleId((int)$data['roleid'] ?? null);
        $this->setUrl((string)$data['url'] ?? '');
        $this->setTimezone((string)$data['timezone'] ?? '');
        $this->setAutologin((int)$data['autologin'] ?? 0);

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
            'surname' => $this->getSurname(),
            'username' => $this->getUsername(),
            'url' => $this->getUrl(),
            'role_id' => $this->getRoleId(),
            'autologin' => $this->getAutologin(),
            'timezone' => $this->getTimezone()
        ];
    }
}