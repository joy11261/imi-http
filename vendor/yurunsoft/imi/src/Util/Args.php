<?php

namespace Imi\Util;

/**
 * 命令行参数操作类.
 */
abstract class Args
{
    /**
     * 参数存储.
     *
     * @var string
     */
    private static $cliArgs;

    /**
     * 初始化.
     *
     * @param int $argBegin 从第几个参数算
     *
     * @return void
     */
    public static function init($argBegin = 1)
    {
        static::$cliArgs = [];
        $keyName = null;
        for ($i = $argBegin; $i < $_SERVER['argc']; ++$i)
        {
            $argvI = $_SERVER['argv'][$i];
            if (isset($argvI[0]) && '-' === $argvI[0])
            {
                $keyName = substr($argvI, 1);
                static::$cliArgs[$keyName] = true;
            }
            else
            {
                if (null === $keyName)
                {
                    static::$cliArgs[$argvI] = true;
                }
                else
                {
                    static::$cliArgs[$keyName] = $argvI;
                    $keyName = null;
                }
            }
        }
    }

    /**
     * 指定数据是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function exists($name)
    {
        if (\is_int($name))
        {
            return isset($_SERVER['argv'][$name]);
        }
        else
        {
            return isset(static::$cliArgs[$name]);
        }
    }

    /**
     * 获取值
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get($name = '', $default = null)
    {
        if (\is_int($name))
        {
            $data = $_SERVER['argv'];
        }
        else
        {
            $data = &static::$cliArgs;
        }
        if ('' === $name)
        {
            // 全部的值
            return $data;
        }
        // 判断指定的值是否存在
        elseif (isset($data[$name]))
        {
            return $data[$name];
        }
        else
        {
            // 返回默认值
            return $default;
        }
    }

    /**
     * 写入参数值
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     */
    public static function set(string $name, ?string $value)
    {
        if (\is_int($name))
        {
            $_SERVER['argv'][$name] = $value;
        }
        else
        {
            static::$cliArgs[$name] = $value;
        }
    }
}
