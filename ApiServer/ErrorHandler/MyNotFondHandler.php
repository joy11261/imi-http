<?php
namespace ImiApp\ApiServer\ErrorHandler;

use Imi\Server\Http\Error\IHttpNotFoundHandler;
use Imi\Util\Http\Consts\ResponseHeader;
use Imi\Util\Stream\FileStream;
use Imi\Util\Imi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyNotFondHandler implements IHttpNotFoundHandler {
    public function handle(RequestHandlerInterface $requesthandler, ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ('./favicon.ico' === $request->getUri()->getPath()) {
            $fs = new FileStream(Imi::getNameSpacePath('ImiApp').'/imi.ico');
            return $response->withHeader(ResponseHeader::CONTENT_TYPE,'image/x-icon')->withBody($fs);
        }

        return $requesthandler->handle($request);
    }
}