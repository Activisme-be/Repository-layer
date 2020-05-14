<?php

namespace Dugajean\Repositories\Helpers;

/**
 * Class CacheKeys
 *
 * @package Dugajean\Repositories\Helpers
 */
class CacheKeys
{
    protected static string $storeFile = 'repository-cache-keys.json';

    protected static ?array $keys = null;

    /**
     * Method for putting a key in the cache.
     *
     * @param  $group
     * @param  $key
     * @return void
     */
    public static function putKey($group, $key): void
    {
        self::loadKeys();
        self::$keys[$group] = self::getKeys($group);

        if (!in_array($key, self::$keys[$group])) {
            self::$keys[$group][] = $key;
        }

        self::storeKeys();
    }

    /**
     * @return array|mixed
     */
    public static function loadKeys()
    {
        if (! is_null(self::$keys) && is_array(self::$keys)) {
            return self::$keys;
        }

        $file = self::getFileKeys();

        if (! file_exists($file)) {
            self::storeKeys();
        }

        $content = file_get_contents($file);
        self::$keys = json_decode($content, true);

        return self::$keys;
    }

    public static function getFileKeys(): string
    {
        $file = storage_path("framework/cache/" . self::$storeFile);

        return $file;
    }

    public static function storeKeys(): int
    {
        $file = self::getFileKeys();
        self::$keys = is_null(self::$keys) ? [] : self::$keys;
        $content = json_encode(self::$keys);

        return file_put_contents($file, $content);
    }

    /**
     * @param $group
     *
     * @return array|mixed
     */
    public static function getKeys($group)
    {
        self::loadKeys();
        self::$keys[$group] = isset(self::$keys[$group]) ? self::$keys[$group] : [];

        return self::$keys[$group];
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;

        return call_user_func_array([
            $instance,
            $method
        ], $parameters);
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $instance = new static;

        return call_user_func_array([
            $instance,
            $method
        ], $parameters);
    }
}
