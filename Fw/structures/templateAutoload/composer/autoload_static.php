<?php
namespace Composer\Autoload;

class ComposerStaticInit
{

    # %FILES%

    # %LENGTHSPSR4%

    # %DIRSPSR4%

    public static $classMap = array (
        'Composer\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit::$classMap;

        }, null, ClassLoader::class);
    }
}