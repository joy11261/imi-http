<?php
namespace ImiApp\ApiServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("Index")
 */
class Index implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ('/index.php' == $request->getUri()->getPath()) {
            return RequestContext::get('response')->write('imi');
        } else {
           return $handler->handle($request);
        }
    }
}