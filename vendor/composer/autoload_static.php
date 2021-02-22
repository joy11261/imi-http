<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita7fbca837e32ca0de9253b875f1f57de
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        'a882399941328d65de1fab340a9720e4' => __DIR__ . '/..' . '/yurunsoft/swoole-co-pool/src/function.php',
        'b3dbf40378b8c1d12a38db3e7c2b7037' => __DIR__ . '/..' . '/yurunsoft/imi/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'Yurun\\Swoole\\CoPool\\' => 20,
            'Yurun\\Doctrine\\Common\\Annotations\\' => 34,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
        ),
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Log\\' => 8,
            'Psr\\Http\\Server\\' => 16,
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Container\\' => 14,
        ),
        'L' => 
        array (
            'Lcobucci\\JWT\\' => 13,
        ),
        'I' => 
        array (
            'Imi\\JWT\\' => 8,
            'Imi\\' => 4,
            'ImiApp\\' => 7,
        ),
        'D' => 
        array (
            'Dotenv\\' => 7,
            'Doctrine\\Common\\Lexer\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Yurun\\Swoole\\CoPool\\' => 
        array (
            0 => __DIR__ . '/..' . '/yurunsoft/swoole-co-pool/src',
        ),
        'Yurun\\Doctrine\\Common\\Annotations\\' => 
        array (
            0 => __DIR__ . '/..' . '/yurunsoft/doctrine-annotations/lib/Doctrine/Common/Annotations',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Http\\Server\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-server-handler/src',
            1 => __DIR__ . '/..' . '/psr/http-server-middleware/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Lcobucci\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/lcobucci/jwt/src',
        ),
        'Imi\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/imiphp/imi-jwt/src',
        ),
        'Imi\\' => 
        array (
            0 => __DIR__ . '/..' . '/yurunsoft/imi/src',
        ),
        'ImiApp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Dotenv\\' => 
        array (
            0 => __DIR__ . '/..' . '/vlucas/phpdotenv/src',
        ),
        'Doctrine\\Common\\Lexer\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/lexer/lib/Doctrine/Common/Lexer',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita7fbca837e32ca0de9253b875f1f57de::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita7fbca837e32ca0de9253b875f1f57de::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
