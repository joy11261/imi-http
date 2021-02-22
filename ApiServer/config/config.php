<?php

use Imi\Log\LogLevel;
return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'ImiApp\ApiServer\Controller',
        'ImiApp\ApiServer\Middleware',
    ],
    'beans'    =>    [
        'SessionManager'    =>    [
            'handlerClass'    =>    \Imi\Server\Session\Handler\File::class,
        ],
        'SessionFile'    =>    [
            'savePath'    =>    dirname(__DIR__, 2) . '/.runtime/.session/',
        ],
        'SessionConfig'    =>    [

        ],
        'SessionCookie'    =>    [
            'lifetime'    =>    86400 * 30,
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