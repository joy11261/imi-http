<?php

namespace Imi\Util;

/**
 * 通道队列类-支持多进程.
 */
abstract class CoroutineChannelManager
{
    /**
     * \Swoole\Coroutine\Channel 数组.
     *
     * @var \Swoole\Coroutine\Channel[]
     */
    protected static $channels = [];

    /**
     * 增加对象名称.
     *
     * @param string $name
     * @param int    $size 通道占用的内存的尺寸，单位为字节。最小值为64K，最大值没有限制
     *
     * @return void
     */
    public static function addName(string $name, int $size = 0)
    {
        static::$channels[$name] = new \Swoole\Coroutine\Channel($size);
    }

    /**
     * 设置对象名称.
     *
     * @param string[] $names
     *
     * @return void
     */
    public static function setNames(array $names)
    {
        foreach ($names as $key => $args)
        {
            static::$channels[$key] = new \Swoole\Coroutine\Channel(...$args);
        }
    }

    /**
     * 获取所有对象名称.
     *
     * @return void
     */
    public static function getNames()
    {
        return array_keys(static::$channels);
    }

    /**
     * 向通道写入数据
     * $data可以为任意PHP变量，当$data是非字符串类型时底层会自动进行串化
     * $data的尺寸超过8K时会启用临时文件存储数据
     * $data必须为非空变量，如空字符串、空数组、0、null、false
     * 写入成功返回true
     * 通道的空间不足时写入失败并返回false.
     *
     * @param string $name
     * @param mixed  $data
     *
     * @return bool
     */
    public static function push(string $name, $data)
    {
        return static::getInstance($name)->push($data);
    }

    /**
     * 弹出数据
     * pop方法无需传入任何参数
     * 当通道内有数据时自动将数据弹出并还原为PHP变量
     * 当通道内没有任何数据时pop会失败并返回false.
     *
     * @param string $name
     * @param float  $timeout
     *
     * @return mixed
     */
    public static function pop(string $name, $timeout = 0)
    {
        return static::getInstance($name)->pop($timeout);
    }

    /**
     * 获取通道的状态
     * 返回一个数组，缓冲通道将包括4项信息，无缓冲通道返回2项信息
     * consumer_num 消费者数量，表示当前通道为空，有N个协程正在等待其他协程调用push方法生产数据
     * producer_num 生产者数量，表示当前通道已满，有N个协程正在等待其他协程调用pop方法消费数据
     * queue_num 通道中的元素数量
     * queue_bytes 通道当前占用的内存字节数.
     *
     * @param string $name
     *
     * @return array
     */
    public static function stats(string $name): array
    {
        return static::getInstance($name)->stats();
    }

    /**
     * 关闭通道。并唤醒所有等待读写的协程。
     * 唤醒所有生产者协程，push方法返回false
     * 唤醒所有消费者协程，pop方法返回false.
     *
     * @param string $name
     *
     * @return void
     */
    public static function close(string $name)
    {
        static::getInstance($name)->close();
    }

    /**
     * 获取实例.
     *
     * @param string $name
     *
     * @return \Swoole\Atomic
     */
    public static function getInstance(string $name): \Swoole\Coroutine\Channel
    {
        $channels = &static::$channels;
        if (!isset($channels[$name]))
        {
            throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
        }

        return $channels[$name];
    }
}
