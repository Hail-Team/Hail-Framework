<?php

namespace Hail\Http\Middleware;

use Hail\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};

class Route implements MiddlewareInterface
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Process a server request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface      $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $this->router->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        return $handler->handle(
            $request->withAttribute('routing', $result)
        );
    }
}