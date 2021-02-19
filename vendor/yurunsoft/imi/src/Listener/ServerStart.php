<?php

namespace Imi\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Listener\IManagerStartEventListener;
use Imi\Server\Event\Param\ManagerStartEventParam;
use Imi\ServerManage;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.MANAGER.START")
 */
class ServerStart implements IManagerStartEventListener
{
    /**
     * 事件处理方法.
     *
     * @param ManagerStartEventParam $e
     *
     * @return void
     */
    public function handle(ManagerStartEventParam $e)
    {
        Imi::setProcessName('master');
        echo 'Server start', \PHP_EOL;
        if (App::isCoServer())
        {
            $data = $e->getData();
            echo 'WorkerNum: ', $data['workerNum'], ', TaskWorkerNum: 0', \PHP_EOL;
            echo '[', $data['config']['type'], '] ', $data['name'], '; listen: ', $data['config']['host'], ':', $data['config']['port'], \PHP_EOL;
        }
        else
        {
            $server = ServerManage::getServer('main');
            $mainSwooleServer = $server->getSwooleServer();
            echo 'WorkerNum: ', $mainSwooleServer->setting['worker_num'], ', TaskWorkerNum: ', $mainSwooleServer->setting['task_worker_num'], \PHP_EOL;
            foreach (ServerManage::getServers() as $server)
            {
                $serverPort = $server->getSwoolePort();
                echo '[', $server->getConfig()['type'], '] ', $server->getName(), '; listen: ', $serverPort->host, ':', $serverPort->port, \PHP_EOL;
            }
        }
    }
}
