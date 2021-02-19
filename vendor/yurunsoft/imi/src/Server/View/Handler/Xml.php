<?php

namespace Imi\Server\View\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;

/**
 * Xml视图处理器.
 *
 * @Bean("XmlView")
 */
class Xml implements IHandler
{
    public function handle($data, array $options, Response $response): Response
    {
        $response = $response->withHeader(ResponseHeader::CONTENT_TYPE, MediaType::APPLICATION_XML);
        if ($data instanceof \DOMDocument)
        {
            return $response->write($data->saveXML());
        }
        elseif ($data instanceof \SimpleXMLElement)
        {
            return $response->write($data->asXML());
        }
        else
        {
            throw new \RuntimeException('Unsupport xml object type: ' . \gettype($data));
        }
    }
}
