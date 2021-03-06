<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit104076f8463e58ad66efff9b33792786
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPGraphQL\\Extensions\\TaxQuery\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPGraphQL\\Extensions\\TaxQuery\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'WPGraphQL\\Extensions\\TaxQuery\\Loader' => __DIR__ . '/../..' . '/src/Loader.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit104076f8463e58ad66efff9b33792786::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit104076f8463e58ad66efff9b33792786::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit104076f8463e58ad66efff9b33792786::$classMap;

        }, null, ClassLoader::class);
    }
}
