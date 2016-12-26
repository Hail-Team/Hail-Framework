<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Hail\Cache\Driver;

use Hail\Cache\Driver;
use \Couchbase as CB;

/**
 * Couchbase cache driver.
 *
 * @link   www.doctrine-project.org
 * @since  2.4
 * @author Michael Nitschinger <michael@nitschinger.at>
 * @author Hao Feng <flyinghail@msn.com>
 */
class Couchbase extends Driver
{
    /**
     * @var Couchbase|null
     */
    private $couchbase;

	public function __construct($params)
	{
		parent::__construct($params);
	}

    /**
     * Sets the Couchbase instance to use.
     *
     * @param Couchbase $couchbase
     *
     * @return void
     */
    public function setCouchbase(CB $couchbase)
    {
        $this->couchbase = $couchbase;
    }

    /**
     * Gets the Couchbase instance used by the cache.
     *
     * @return Couchbase|null
     */
    public function getCouchbase()
    {
        return $this->couchbase;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return $this->couchbase->get($id) ?: false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return (null !== $this->couchbase->get($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifetime = 0)
    {
        if ($lifetime > 30 * 24 * 3600) {
            $lifetime = time() + $lifetime;
        }
        return $this->couchbase->set($id, $data, (int) $lifetime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return $this->couchbase->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return $this->couchbase->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        $stats   = $this->couchbase->getStats();
        $servers = $this->couchbase->getServers();
        $server  = explode(":", $servers[0]);
        $key     = $server[0] . ":" . "11210";
        $stats   = $stats[$key];
        return [
	        Driver::STATS_HITS   => $stats['get_hits'],
	        Driver::STATS_MISSES => $stats['get_misses'],
	        Driver::STATS_UPTIME => $stats['uptime'],
	        Driver::STATS_MEMORY_USAGE     => $stats['bytes'],
	        Driver::STATS_MEMORY_AVAILABLE => $stats['limit_maxbytes'],
        ];
    }
}