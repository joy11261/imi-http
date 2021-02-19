<?php

namespace Imi\Server\Group\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\ConnectContext;
use Imi\Server\ConnectContext\Event\Listener\IConnectContextRestoreListener;
use Imi\Server\ConnectContext\Event\Param\ConnectContextRestoreParam;
use Imi\ServerManage;

/**
 * @Listener(eventName="IMI.CONNECT_CONTEXT.RESTORE")
 */
class GroupRestore implements IConnectContextRestoreListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(ConnectContextRestoreParam $e)
    {
        $fromFd = $e->fromFd;
        $toFd = $e->toFd;
        $connectContextData = ConnectContext::getContext($fromFd);
        $groups = $connectContextData['__groups'] ?? [];
        if (!$groups)
        {
            return;
        }
        $server = ServerManage::getServer($connectContextData['__serverName']);
        foreach ($groups as $group)
        {
            $server->joinGroup($group, $toFd);
        }
    }
}
