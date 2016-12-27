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

use Hail\Cache\TaggableItemInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class CacheItem implements PhpCacheItem, TaggableItemInterface
{
    /**
     * @type array
     */
    private $tags = [];

    /**
     * @type \Closure
     */
    private $callable;

    /**
     * @type string
     */
    private $key;

    /**
     * @type mixed
     */
    private $value;

    /**
     * The expiration timestamp is the source of truth. This is the UTC timestamp
     * when the cache item expire. A value of zero means it never expires. A nullvalue
     * means that no expiration is set.
     *
     * @type int|null
     */
    private $expirationTimestamp = null;

    /**
     * @type bool
     */
    private $hasValue = false;

    /**
     * @param string        $key
     * @param \Closure|bool $callable or boolean hasValue
     */
    public function __construct($key, $callable = null, $value = null)
    {
        $this->key = $key;

        if ($callable === true) {
            $this->hasValue = true;
            $this->value    = $value;
        } elseif ($callable !== false) {
            // This must be a callable or null
            $this->callable = $callable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value)
    {
        $this->value    = $value;
        $this->hasValue = true;
        $this->callable = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if (!$this->isHit()) {
            return;
        }

        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit()
    {
        $this->initialize();

        if (!$this->hasValue) {
            return false;
        }

        if ($this->expirationTimestamp !== null) {
            return $this->expirationTimestamp > time();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationTimestamp()
    {
        return $this->expirationTimestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expiration)
    {
        if ($expiration instanceof \DateTimeInterface) {
            $this->expirationTimestamp = $expiration->getTimestamp();
        } else {
            $this->expirationTimestamp = $expiration;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time)
    {
        if ($time === null) {
            $this->expirationTimestamp = null;
        } elseif ($time instanceof \DateInterval) {
            $date = new \DateTime();
            $date->add($time);
            $this->expirationTimestamp = $date->getTimestamp();
        } elseif (is_int($time)) {
            $this->expirationTimestamp = time() + $time;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        $this->initialize();

        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function addTag($tag)
    {
        $this->initialize();

        $this->tags[] = $tag;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTags(array $tags)
    {
        $this->initialize();

        $this->tags = $tags;

        return $this;
    }

    /**
     * If callable is not null, execute it an populate this object with values.
     */
    private function initialize()
    {
        if ($this->callable !== null) {
            // $f will be $adapter->fetchObjectFromCache();
            $f                         = $this->callable;
            $result                    = $f();
            $this->hasValue            = $result[0];
            $this->value               = $result[1];
            $this->tags                = isset($result[2]) ? $result[2] : [];
            $this->expirationTimestamp = null;

            if (isset($result[3]) && is_int($result[3])) {
                $this->expirationTimestamp = $result[3];
            }

            $this->callable = null;
        }
    }
}
