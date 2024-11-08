<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit15b05aefe34991fbf539fe679e8a5317
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'AdvancedGallery\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'AdvancedGallery\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit15b05aefe34991fbf539fe679e8a5317::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit15b05aefe34991fbf539fe679e8a5317::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit15b05aefe34991fbf539fe679e8a5317::$classMap;

        }, null, ClassLoader::class);
    }
}
