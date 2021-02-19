<?php

namespace Imi\Server\Http\Listener;

use Imi\RequestContext;
use Imi\Server\Event\Listener\ICloseEventListener;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Worker;

class Http2BeforeClose implements ICloseEventListener
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
