<?php declare(strict_types=1);

namespace Starlit\App;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareAwareInterface
{
    /**
     * @param MiddlewareInterface $middleware
     */
    public function registerMiddleware(MiddlewareInterface $middleware): void;

    /**
     * @return MiddlewareInterface[]
     */
    public function getMiddlewares(): array;

    /**
     * @param MiddlewareInterface $middleware
     */
    public function registerRouteMiddleware(MiddlewareInterface $middleware): void;

    /**
     * @return MiddlewareInterface[]
     */
    public function getRouteMiddlewares(): array;
}
