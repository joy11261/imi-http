<?php
namespace ImiApp\ApiServer\ErrorHandler;

use Imi\RequestContext;
use Imi\Server\Http\Error\IErrorHandler;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;

class MyErrorHandler implements IErrorHandler {

    public function handle(\Throwable $throwable): bool
    {
        // TODO: Implement handle() method.
        /**
         * @var \Imi\Server\Http\Message\Response $response
         */
        $response = RequestContext::get('response');
        $response->withHeader(ResponseHeader::CONTENT_TYPE,MediaType::APPLICATION_JSON_UTF8)
            ->write(json_encode([
                'success' => false,
                'error' => $throwable->getMessage()
            ]))
            ->send();
        return true;
    }
}