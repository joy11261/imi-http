<?php

namespace Imi\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\Event;
use Imi\Server\Event\Listener\IPipeMessageEventListener;
use Imi\Server\Event\Param\PipeMessageEventParam;

/**
 * @Listener("IMI.MAIN_SERVER.PIPE_MESSAGE")
 */
class OnPipeMessage implements IPipeMessageEventListener
{
    /**
     * 事件处理方法.
     *
     * @param PipeMessageEventParam $e
     *
     * @return void
     */
    public function handle(PipeMessageEventParam $e)
    {
        $data = json_decode($e->message, true);
        $action = $data['action'] ?? null;
        if (!$action)
        {
            return;
        }
        Event::trigger('IMI.PIPE_MESSAGE.' . $action, [
            'workerID'  => $e->workerID,
            'data'      => $data,
        ]);
    }
}
