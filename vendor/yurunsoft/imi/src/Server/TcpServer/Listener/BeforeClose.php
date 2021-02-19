<?php

namespace Imi\Server\TcpServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\RequestContext;
use Imi\Server\Event\Listener\ICloseEventListener;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Worker;

/**
 * Close事件前置处理.
 *
 * @ClassEventListener(className="Imi\Server\TcpServer\Server",eventName="close",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeClose implements ICloseEventListener
{
    /**
     * 事件处理方法.
     *
     * @param CloseEventParam $e
     *
     * @return void
     */
    public function handle(CloseEventParam $e)
    {
        if (!Worker::isWorkerStartAppComplete())
        {
            $e->stopPropagation();

            return;
        }
        RequestContext::muiltiSet([
            'fd'        => $e->fd,
            'server'    => $e->getTarget(),
        ]);
    }
}
