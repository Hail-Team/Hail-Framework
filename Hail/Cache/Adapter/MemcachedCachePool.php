<?php

/*
 * This file is part of php-cache organization.
 *
 * (c) 2015-2016 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hail\Cache\Adapter;

use Hail\Cache\HierarchicalCachePoolTrait;
use Hail\Cache\HierarchicalPoolInterface;
use Hail\Facades\Serialize;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MemcachedCachePool extends AbstractCachePool implements HierarchicalPoolInterface
{
    use HierarchicalCachePoolTrait;

    /**
     * @type \Memcached
     */
    protected $cache;

    /**
     * @param \Memcached $cache
     */
    public function __construct(\Memcached $cache)
    {
        $this->cache = $cache;
        $this->cache->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchObjectFromCache($key)
    {
        if (false === $result = Serialize::decode($this->cache->get($this->getHierarchyKey($key)))) {
            return [false, null, [], null];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function clearAllObjectsFromCache()
    {
        return $this->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function clearOneObjectFromCache($key)
    {
        $this->commit();
        $key = $this->getHierarchyKey($key, $path);
        $this->cache->increment($path, 1, 0);
        $this->clearHierarchyKeyCache();

        if ($this->cache->delete($key)) {
            return true;
        }

        // Return true if key not found
        return $this->cache->getResultCode() === \Memcached::RES_NOTFOUND;
    }

    /**
     * {@inheritdoc}
     */
    protected function storeItemInCache(PhpCacheItem $item, $ttl)
    {
        if ($ttl === null) {
            $ttl = 0;
        } elseif ($ttl < 0) {
            return false;
        } elseif ($ttl > 86400 * 30) {
            // Any time higher than 30 days is interpreted as a unix timestamp date.
            // https://github.com/memcached/memcached/wiki/Programming#expiration
            $ttl = time() + $ttl;
        }

        $key = $this->getHierarchyKey($item->getKey());

        return $this->cache->set($key, Serialize::encode([true, $item->get(), [], $item->getExpirationTimestamp()]), $ttl);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValueFormStore($key)
    {
        return $this->cache->get($key);
    }
}