<?php

namespace Imi\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Listener\IWorkerExitEventListener;
use Imi\Server\Event\Param\WorkerExitEventParam;
use Imi\Util\ImiPriority;
use Swoole\Timer;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.EXIT", priority=ImiPriority::IMI_MIN)
 */
class WorkerExit implements IWorkerExitEventListener
{
    /**
     * 事件处理方法.
     *
     * @param WorkerExitEventParam $e
     *
     * @return void
     */
    public function handle(WorkerExitEventParam $e)
    {
        Timer::clearAll();
    }
}
