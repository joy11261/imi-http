<?php

use Imi\Log\LogLevel;
return [
    'configs'    =>    [
    ],
    // bean扫描目录   增加一个bean时  需要在这里加入命名空间  否则扫描不到这个bean
    'beanScan'    =>    [
        'ImiApp\ApiServer\Controller',
        'ImiApp\ApiServer\Middleware',
        'ImiApp\ApiServer\Validation',
    ],
    'beans'    =>    [
        'SessionManager'    =>    [
            // 指定 Session 存储驱动类
            'handlerClass'    =>    \Imi\Server\Session\Handler\Redis::class,
        ],
        'SessionConfig'    =>    [
            // session 名称，默认为imisid
            'name'    =>    'imisid',
            // 每次请求完成后触发垃圾回收的概率，默认为1%，可取值0~1.0，概率为0%~100%
            'gcProbability'    =>    0.01,
            // 最大存活时间，默认30天，单位秒
            'maxLifeTime'=>    86400 * 30,
            // session 前缀
            'prefix' => null,
        ],
        'SessionCookie'    =>    [
            // 是否启用 Cookie
            'enable'    =>  true,
            // Cookie 的 生命周期，以秒为单位。
            'lifetime'    =>    0,
            // 此 cookie 的有效 路径。 on the domain where 设置为“/”表示对于本域上所有的路径此 cookie 都可用。
            'path'        =>    '/',
            // Cookie 的作用 域。 例如：“www.php.net”。 如果要让 cookie 在所有的子域中都可用，此参数必须以点（.）开头，例如：“.php.net”。
            'domain'    =>    '',
            // 设置为 TRUE 表示 cookie 仅在使用 安全 链接时可用。
            'secure'    =>    false,
            // 设置为 TRUE 表示 PHP 发送 cookie 的时候会使用 httponly 标记。
            'httponly'    =>    false,
        ],
        'SessionRedis'    =>    [
            // Redis连接池名称
            'poolName'    =>    '',
            // Redis中存储的key前缀，可以用于多系统session的分离
            'keyPrefix'    =>    'imi:',
            'formatHandlerClass'    =>    \Imi\Util\Format\Json::class,
        ],
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                \ImiApp\ApiServer\Middleware\PoweredBy::class,
                \ImiApp\ApiServer\Middleware\Index::class,
                \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'HtmlView'    =>    [
            'templatePath'    =>    dirname(__DIR__) . '/template/',
            // 支持的模版文件扩展名，优先级按先后顺序
            'fileSuffixs'        =>    [
                'tpl',
                'html',
                'php'
            ],
        ],
        'HttpErrorHandler'    =>    [
            // 指定默认处理器
            'handler'    =>   \ImiApp\ApiServer\ErrorHandler\MyErrorHandler::class,
        ],
        'HttpNotFoundHandler'    =>    [
            // 指定默认处理器
            'handler'    =>    \ImiApp\ApiServer\ErrorHandler\MyNotFondHandler::class,
        ],
    ],
    'middleware' => [
        'groups' => [
            'test1' => [
                'C',
                'D',
            ],
        ],
    ]
];