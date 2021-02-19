<?php

namespace Imi\Controller;

/**
 * WebSocket 控制器.
 */
abstract class WebSocketController
{
    /**
     * 服务器.
     *
     * @var \Imi\Server\WebSocket\Server
     */
    public $server;

    /**
     * 桢.
     *
     * @var \Imi\Server\WebSocket\Message\IFrame
     */
    public $frame;
}
