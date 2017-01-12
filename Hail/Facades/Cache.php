<?php
namespace Hail\Facades;

use Hail\Factory\CacheFactory;

/**
 * Class Cache
 *
 * @package Hail\Facades
 *
 * @method static void setNamespace(string $namespace)
 * @method static string getNamespace()
 * @method static int ttl(null|int|\DateInterval $ttl)
 * @method static mixed get(string $key, $default = null)
 * @method static array getMultiple(array $keys, $default = null)
 * @method static bool has(string $key)
 * @method static bool setMultiple(iterable $values, null|int|\DateInterval $ttl = null)
 * @method static bool set(string $key, mixed $values, null|int|\DateInterval $ttl = null)
 * @method static bool delete(string $key)
 * @method static bool deleteMultiple(array $keys)
 * @method static bool clear()
 * @method static bool deleteAll()
 */
class Cache extends Facade
{
	protected static function instance()
	{
		return CacheFactory::simple();
	}
}