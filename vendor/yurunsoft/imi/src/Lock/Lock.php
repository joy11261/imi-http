<?php

namespace Imi\Lock;

use Imi\App;
use Imi\Config;

abstract class Lock
{
    /**
     * 配置.
     *
     * @var \Imi\Lock\LockConfigOption[]
     */
    private static $options = [];

    /**
     * 对象列表.
     *
     * @var \Imi\Lock\Handler\ILockHandler[]
     */
    private static $instances = [];

    /**
     * 获取锁对象
     *
     * @param string|null $lockConfigId
     * @param string|null $lockId
     *
     * @return \Imi\Lock\Handler\ILockHandler
     */
    public static function getInstance($lockConfigId = null, $lockId = null)
    {
        if (!$lockConfigId)
        {
            $lockConfigId = static::getDefaultId();
        }
        $instances = &static::$instances;
        if (null === $lockId && isset($instances[$lockConfigId]))
        {
            return $instances[$lockConfigId];
        }
        $options = &static::$options;
        if (!isset($options[$lockConfigId]))
        {
            throw new \RuntimeException(sprintf('Lock %s does not exists', $lockConfigId));
        }
        $option = $options[$lockConfigId];
        if (null === $lockId)
        {
            return $instances[$lockConfigId] = App::getBean($option->class, $lockConfigId, $option->options);
        }
        else
        {
            return App::getBean($option->class, $lockId, $option->options);
        }
    }

    /**
     * 获取默认锁ID.
     *
     * @return void
     */
    public static function getDefaultId()
    {
        return Config::get('@currentServer.lock.default');
    }

    /**
     * 增加配置.
     *
     * @param string $id
     * @param array  $option
     *
     * @return void
     */
    public static function add($id, $option)
    {
        static::$options[$id] = new LockConfigOption($option);
    }

    /**
     * 加锁，会挂起协程.
     *
     * @param string|null $id
     * @param callable    $taskCallable      加锁后执行的任务，可为空；如果不为空，则执行完后自动解锁
     * @param callable    $afterLockCallable 当获得锁后执行的回调，只有当 $taskCallable 不为 null 时有效。该回调返回 true 则不执行 $taskCallable
     *
     * @return bool
     */
    public static function lock($id = null, $taskCallable = null, $afterLockCallable = null)
    {
        return static::getInstance($id)->lock($taskCallable, $afterLockCallable);
    }

    /**
     * 尝试获取锁
     *
     * @param string|null $id
     * @param callable    $taskCallable 加锁后执行的任务，可为空；如果不为空，则执行完后自动解锁
     *
     * @return bool
     */
    public static function tryLock($id = null, $taskCallable = null)
    {
        return static::getInstance($id)->tryLock($taskCallable);
    }

    /**
     * 解锁
     *
     * @param string|null $id
     *
     * @return bool
     */
    public static function unlock($id = null)
    {
        return static::getInstance($id)->unlock($id);
    }

    /**
     * 获取当前是否已获得锁状态
     *
     * @param string|null $id
     *
     * @return bool
     */
    public static function isLocked($id = null)
    {
        return static::getInstance($id)->isLocked();
    }
}
