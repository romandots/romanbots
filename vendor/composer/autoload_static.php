<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd1b1b76ade042c54b27f1cf73965e794
{
    public static $files = array (
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        'e4b7bad8afd3c152315c0e646349e76a' => __DIR__ . '/../..' . '/global.php',
        '50d1ad175834a08f479292afcc23e5b2' => __DIR__ . '/../..' . '/config.php',
        'afe8958fef5448b6018bb80e5bb1b18c' => __DIR__ . '/../..' . '/Tansultant.php',
        '6999988b34d08169fefa45940dffe8f6' => __DIR__ . '/../..' . '/Bot.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SpeechKit\\' => 10,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SpeechKit\\' => 
        array (
            0 => __DIR__ . '/..' . '/zloesabo/speechkit-php/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd1b1b76ade042c54b27f1cf73965e794::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd1b1b76ade042c54b27f1cf73965e794::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
