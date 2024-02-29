<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubBranchState extends Model
{
    protected $primaryKey = ['repository_id', 'branch_name'];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public $incrementing = false;

    protected $fillable = [
        'github_organization_id',
        'repository_id',
        'branch_name',
        'ahead_by',
        'status',
        'behind_by',
        'last_commit_author_username',
        'last_commit_time',
    ];

    /**
     * Set the keys for a save update query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (! is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     *
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    public function getKey()
    {
        $attributes = [];
        foreach ($this->getKeyName() as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }

        return $attributes;
    }
}
