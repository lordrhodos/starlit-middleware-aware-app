<?php declare(strict_types=1);

namespace Starlit\App;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Decorator class adding PSR-15 middleware functionality to the original starlit application class
 *
 * @author Patrick Rodacker <github@rodacker.de>
 */
class MiddlewareAwareApp extends BaseApp implements MiddlewareAwareInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * MiddlewareAwareApp constructor.
     *
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(
        $config = [],
        $environment = 'production',
        RequestHandlerInterface $requestHandler = null
    ) {
        parent::__construct($config, $environment);
        $this->requestHandler = $requestHandler ?? new QueueRequestHandler();
    }

    /**
     * Handles an http request and returns a response.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $this->set('request', $request);

        $this->boot();

        if (($preHandleResponse = $this->preHandle($request))) {
            return $preHandleResponse;
        }

        try {
            $controller = $this->getRouter()->route($request);

            if (($postRouteResponse = $this->postRoute($request))) {
                return $postRouteResponse;
            }

            $response = $controller->dispatch();
        } catch (ResourceNotFoundException $e) {
            $response = $this->getNoRouteResponse($request);
        }

        $this->postHandle($request);

        return $response;
    }
}
