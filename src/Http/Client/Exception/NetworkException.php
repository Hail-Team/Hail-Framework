<?php

namespace Hail\Http\Client\Exception;

use Hail\Http\Client\Psr\Exception\NetworkException as PsrNetworkException;

/**
 * Thrown when the request cannot be completed because of network issues.
 *
 * There is no response object as this exception is thrown when no response has been received.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class NetworkException extends RequestException implements PsrNetworkException
{
}
