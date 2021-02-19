<?php

namespace Imi\Server\UdpServer;

use Imi\RequestContext;
use Imi\Server\UdpServer\Message\IPacketData;

class PacketHandler implements IPacketHandler
{
    /**
     * 中间件数组.
     *
     * @var string[]
     */
    protected $middlewares = [];

    /**
     * 当前执行第几个.
     *
     * @var int
     */
    protected $index = 0;

    /**
     * 构造方法.
     *
     * @param string[] $middlewares 中间件数组
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * 返回值为响应内容，为null则无任何响应.
     *
     * @param IPacketData $data
     *
     * @return mixed
     */
    public function handle(IPacketData $data)
    {
        $middlewares = &$this->middlewares;
        $index = $this->index;
        if (isset($middlewares[$index]))
        {
            $middleware = $middlewares[$index];
            if (\is_object($middleware))
            {
                $requestHandler = $middleware;
            }
            else
            {
                $requestHandler = RequestContext::getServerBean($middleware);
            }
        }
        else
        {
            return null;
        }

        return $requestHandler->process($data, $this->next());
    }

    /**
     * 获取下一个RequestHandler对象
     *
     * @return static
     */
    protected function next()
    {
        $self = clone $this;
        ++$self->index;

        return $self;
    }

    /**
     * 是否是最后一个.
     *
     * @return bool
     */
    public function isLast()
    {
        return !isset($this->middlewares[$this->index]);
    }
}
