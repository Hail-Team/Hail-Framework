<?php

declare(strict_types=1);

namespace Hail\Http\Factory;

use Interop\Http\Factory\UriFactoryInterface;
use Hail\Http\Factory;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class UriFactory implements UriFactoryInterface
{
    public function createUri($uri = '')
    {
        return Factory::uri($uri);
    }
}
