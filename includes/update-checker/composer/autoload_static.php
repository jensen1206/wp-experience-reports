<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcec4c267675a1cb476661cd0a80e4610
{
    public static $files = array (
        '49a1299791c25c6fd83542c6fedacddd' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v4p11.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitcec4c267675a1cb476661cd0a80e4610::$classMap;

        }, null, ClassLoader::class);
    }
}
