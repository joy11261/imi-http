<?php

use Imi\Log\LogLevel;
$rootPath = dirname(__DIR__) . '/';
return [
    'hotUpdate'    =>    [
        // 'status'    =>    false, // 关闭热更新去除注释，不设置即为开启，建议生产环境关闭

        // --- 文件修改时间监控 ---
        // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\FileMTime::class,
        'timespan'    =>    1, // 检测时间间隔，单位：秒

        // --- Inotify 扩展监控 ---
        // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\Inotify::class,
        // 'timespan'    =>    1, // 检测时间间隔，单位：秒，使用扩展建议设为0性能更佳

        // 'includePaths'    =>    [], // 要包含的路径数组
        'excludePaths'    =>    [
            $rootPath . '.git',
            $rootPath . 'bin',
            $rootPath . 'logs',
        ], // 要排除的路径数组，支持通配符*
    ],
    'Logger'    =>    [
        'exHandlers'    =>    [
            // info 级别日志不输出trace
            [
                'class'        =>    \Imi\Log\Handler\File::class,
                'options'    =>    [
                    'levels'        => [LogLevel::INFO],
                    'fileName'      => dirname(__DIR__) . '/.runtime/logs/{Y}-{m}-{d}.log',
                    'format'        => "{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}",
                ],
            ],
            // 指定级别日志输出trace
            [
                'class'        =>    \Imi\Log\Handler\File::class,
                'options'    =>    [
                    'levels'        => [
                        LogLevel::ALERT,
                        LogLevel::CRITICAL,
                        LogLevel::DEBUG,
                        LogLevel::EMERGENCY,
                        LogLevel::ERROR,
                        LogLevel::NOTICE,
                        LogLevel::WARNING,
                    ],
                    'fileName'      => dirname(__DIR__) . '/.runtime/logs/{Y}-{m}-{d}.log',
                    'format'        => "{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}\n{trace}",
                    'traceFormat'   => '#{index}  {call} called at [{file}:{line}]',
                    'traceMinimum'  =>  true,
                ],
            ]
        ],
    ],
    'ErrorLog' => [
        'level' => E_ALL,
    ],
    'JWT'   =>  [
        'list'  =>  [
            // a 为名称，可以自定义，以下被注释的项为非必设，一般有默认值
            'test' =>  [
                // 'signer'    =>  'Hmac',      // 签名者，可选：Ecdsa/Hmac/Rsa
                // 'algo'      =>  'Sha256',    // 算法，可选：Sha256/Sha384/Sha512
                // 'dataName'  =>  'data',      // 自定义数据字段名，放你需要往token里丢的数据
                // 'audience'  =>  null,        // 接收，非必须
                // 'subject'   =>  null,        // 主题，非必须
                // 'expires'   =>  null,        // 超时秒数，非必须
                // 'issuer'    =>  null,        // 发行人，非必须
                // 'notBefore' =>  null,        // 实际日期必须大于等于本值
                // 'issuedAt'  =>  true,        // JWT 发出时间。设为 true 则为当前时间；设为 false 不设置；其它值则直接写入
                // 'id'        =>  null,        // Token id
                // 'headers'   =>  [],          // 头
                // 自定义获取 token 回调，返回值为 Token。默认从 Header Authorization 中获取。
                // 'tokenHandler'  =>  null,
                'privateKey'    =>  '123456',// 私钥
                'publicKey'     =>  '123456',// 公钥
            ],
        ],
    ],
];