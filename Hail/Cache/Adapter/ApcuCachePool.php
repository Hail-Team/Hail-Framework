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

use Hail\Facades\Serialize;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ApcuCachePool extends AbstractCachePool
{
	/**
	 * {@inheritdoc}
	 */
	protected function fetchObjectFromCache($key)
	{
		$success = false;
		$cacheData = apcu_fetch($key, $success);
		if (!$success) {
			return [false, null, [], null];
		}
		list($data, $timestamp, $tags) = Serialize::decode($cacheData);

		return [$success, $data, $tags, $timestamp];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function clearAllObjectsFromCache()
	{
		return apcu_clear_cache();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function clearOneObjectFromCache($key)
	{
		apcu_delete($key);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function storeItemInCache(PhpCacheItem $item, $ttl)
	{
		if ($ttl < 0) {
			return false;
		}

		return apcu_store($item->getKey(), Serialize::encode([$item->get(), $item->getExpirationTimestamp(), []]), $ttl);
	}
}