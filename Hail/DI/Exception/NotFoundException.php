<?php
namespace Hail\DI\Exception;

use Psr\Container\NotFoundException as PsrNotFoundException;

/**
 * Class NotFound
 *
 * @package Hail\DI\Exception
 * @author  Hao Feng <flyinghail@msn.com>
 */
class NotFoundException extends \InvalidArgumentException implements PsrNotFoundException
{

}