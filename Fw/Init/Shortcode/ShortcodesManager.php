<?php
/**
 * Administra la creación automática de los Shortcodes.
 * 
 */
namespace Fw\Init\Shortcode;

use Fw\Init\Shortcode\Shortcodes;
use Fw\Paths;

class ShortcodesManager
{
    /**
     * Valida y prepara los argumentos para la creación de los Shortcodes.
     *
     * @param string $namespace Namespace base de la aplicación.
     * @param string $shortcodesPath Ruta en la app de los Shortcodes.
     * @return void
     **/
    public static function initialize(string $namespace, string $shortcodesPath) : void
    {
        if ( !file_exists($shortcodesPath) ) {
            return;
        }

        # Folers.
        if ( !$dirs = array_diff(scandir($shortcodesPath), array('.', '..')) ) {
            return;
        }

        $shortcodes = [];
        foreach ($dirs as $directory) {
            $mainPath = Paths::buildPath($shortcodesPath, $directory);
            if ( !$files = glob(Paths::buildPath($mainPath, '*.php')) ) {
                continue;
            }

            $shortcodes[$directory] = $files;
        }
        
        # Si Shortcodes disponibles. 
        if ( count($shortcodes) > 0 ) {
            $shortcodes['path'] = Paths::findPluginPath($shortcodesPath);
            Shortcodes::createShortcodes($namespace, $shortcodes);
        }
    }

}
