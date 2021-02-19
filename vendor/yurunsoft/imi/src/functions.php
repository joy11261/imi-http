<?php

use Imi\RequestContext;

/**
 * 启动一个协程，自动创建和销毁上下文.
 *
 * @param callable $callable
 * @param mixed    $args
 *
 * @return void
 */
function imigo(callable $callable, ...$args)
{
    $newCallable = imiCallable($callable);

    return go(function (...$args) use ($newCallable) {
        $newCallable(...$args);
    }, ...$args);
}

/**
 * 为传入的回调自动创建和销毁上下文，并返回新的回调.
 *
 * @param callable $callable
 * @param bool     $withGo   是否内置启动一个协程，如果为true，则无法获取回调返回值
 *
 * @return callable
 */
function imiCallable(callable $callable, bool $withGo = false)
{
    $server = RequestContext::get('server');
    $resultCallable = function (...$args) use ($callable, $server) {
        RequestContext::set('server', $server);

        return $callable(...$args);
    };
    if ($withGo)
    {
        return function (...$args) use ($resultCallable) {
            return go(function (...$args) use ($resultCallable) {
                return $resultCallable(...$args);
            }, ...$args);
        };
    }
    else
    {
        return $resultCallable;
    }
}

/**
 * getenv() 函数的封装，支持默认值
 *
 * @param string $varname
 * @param mixed  $default
 * @param bool   $localOnly
 */
function imiGetEnv($varname = null, $default = null, $localOnly = false)
{
    $result = getenv($varname, $localOnly);
    if (false === $result)
    {
        return $default;
    }

    return $result;
}

/**
 * 处理命令行，执行后不会有 sh 进程
 * 仅限 Swoole >= 4.5.3.
 *
 * @param string $cmd
 *
 * @return string
 */
function cmd(string $cmd): string
{
    return (\SWOOLE_VERSION_ID >= 40503 ? 'exec ' : '') . $cmd;
}
