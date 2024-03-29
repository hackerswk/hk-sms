<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit09e0652e859e9a45e736fbad271bffc7
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stanleysie\\HkSms\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stanleysie\\HkSms\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit09e0652e859e9a45e736fbad271bffc7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit09e0652e859e9a45e736fbad271bffc7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit09e0652e859e9a45e736fbad271bffc7::$classMap;

        }, null, ClassLoader::class);
    }
}
