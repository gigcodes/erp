<?php

namespace Modules\BookStack\Settings;

use Illuminate\Contracts\Cache\Repository as Cache;

/**
 * Class SettingService
 * The settings are a simple key-value database store.
 * For non-authenticated users, user settings are stored via the session instead.
 */
class SettingService
{
    protected $setting;

    protected $cache;

    protected $localCache = [];

    protected $cachePrefix = 'setting-';

    /**
     * SettingService constructor.
     */
    public function __construct(Setting $setting, Cache $cache)
    {
        $this->setting = $setting;
        $this->cache   = $cache;
    }

    /**
     * Gets a setting from the database,
     * If not found, Returns default, Which is false by default.
     *
     * @param string|bool $default
     * @param mixed       $key
     *
     * @return bool|string
     */
    public function get($key, $default = false)
    {
        if ($default === false) {
            $default = config('setting-defaults.' . $key, false);
        }

        if (isset($this->localCache[$key])) {
            return $this->localCache[$key];
        }

        $value                  = $this->getValueFromStore($key, $default);
        $formatted              = $this->formatValue($value, $default);
        $this->localCache[$key] = $formatted;

        return $formatted;
    }

    /**
     * Get a value from the session instead of the main store option.
     *
     * @param bool  $default
     * @param mixed $key
     *
     * @return mixed
     */
    protected function getFromSession($key, $default = false)
    {
        $value     = session()->get($key, $default);
        $formatted = $this->formatValue($value, $default);

        return $formatted;
    }

    /**
     * Get a user-specific setting from the database or cache.
     *
     * @param \BookStack\Auth\User $user
     * @param bool                 $default
     * @param mixed                $key
     *
     * @return bool|string
     */
    public function getUser($user, $key, $default = false)
    {
        if ($user->isDefault()) {
            return $this->getFromSession($key, $default);
        }

        return $this->get($this->userKey($user->id, $key), $default);
    }

    /**
     * Get a value for the current logged-in user.
     *
     * @param bool  $default
     * @param mixed $key
     *
     * @return bool|string
     */
    public function getForCurrentUser($key, $default = false)
    {
        return $this->getUser(user(), $key, $default);
    }

    /**
     * Gets a setting value from the cache or database.
     * Looks at the system defaults if not cached or in database.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    protected function getValueFromStore($key, $default)
    {
        // Check for an overriding value
        $overrideValue = $this->getOverrideValue($key);
        if ($overrideValue !== null) {
            return $overrideValue;
        }

        // Check the cache
        $cacheKey = $this->cachePrefix . $key;
        $cacheVal = $this->cache->get($cacheKey, null);
        if ($cacheVal !== null) {
            return $cacheVal;
        }

        // Check the database
        $settingObject = $this->getSettingObjectByKey($key);
        if ($settingObject !== null) {
            $value = $settingObject->val;
            $this->cache->forever($cacheKey, $value);

            return $value;
        }

        return $default;
    }

    /**
     * Clear an item from the cache completely.
     *
     * @param mixed $key
     */
    protected function clearFromCache($key)
    {
        $cacheKey = $this->cachePrefix . $key;
        $this->cache->forget($cacheKey);
        if (isset($this->localCache[$key])) {
            unset($this->localCache[$key]);
        }
    }

    /**
     * Format a settings value
     *
     * @param mixed $value
     * @param mixed $default
     *
     * @return mixed
     */
    protected function formatValue($value, $default)
    {
        // Change string booleans to actual booleans
        if ($value === 'true') {
            $value = true;
        }
        if ($value === 'false') {
            $value = false;
        }

        // Set to default if empty
        if ($value === '') {
            $value = $default;
        }

        return $value;
    }

    /**
     * Checks if a setting exists.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        $setting = $this->getSettingObjectByKey($key);

        return $setting !== null;
    }

    /**
     * Check if a user setting is in the database.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function hasUser($key)
    {
        return $this->has($this->userKey($key));
    }

    /**
     * Add a setting to the database.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return bool
     */
    public function put($key, $value)
    {
        $setting = $this->setting->firstOrNew([
            'name' => $key,
        ]);
        $setting->val = $value;
        $setting->save();
        $this->clearFromCache($key);

        return true;
    }

    /**
     * Put a user-specific setting into the database.
     *
     * @param \BookStack\Auth\User $user
     * @param mixed                $key
     * @param mixed                $value
     *
     * @return bool
     */
    public function putUser($user, $key, $value)
    {
        if ($user->isDefault()) {
            return session()->put($key, $value);
        }

        return $this->put($this->userKey($user->id, $key), $value);
    }

    /**
     * Convert a setting key into a user-specific key.
     *
     * @param mixed $userId
     * @param mixed $key
     *
     * @return string
     */
    protected function userKey($userId, $key = '')
    {
        return 'user:' . $userId . ':' . $key;
    }

    /**
     * Removes a setting from the database.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function remove($key)
    {
        $setting = $this->getSettingObjectByKey($key);
        if ($setting) {
            $setting->delete();
        }
        $this->clearFromCache($key);

        return true;
    }

    /**
     * Delete settings for a given user id.
     *
     * @param mixed $userId
     *
     * @return mixed
     */
    public function deleteUserSettings($userId)
    {
        return $this->setting->where('name', 'like', $this->userKey($userId) . '%')->delete();
    }

    /**
     * Gets a setting model from the database for the given key.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    protected function getSettingObjectByKey($key)
    {
        return $this->setting->where('name', '=', $key)->first();
    }

    /**
     * Returns an override value for a setting based on certain app conditions.
     * Used where certain configuration options overrule others.
     * Returns null if no override value is available.
     *
     * @param mixed $key
     *
     * @return bool|null
     */
    protected function getOverrideValue($key)
    {
        if ($key === 'registration-enabled' && config('auth.method') === 'ldap') {
            return false;
        }

        return null;
    }
}
