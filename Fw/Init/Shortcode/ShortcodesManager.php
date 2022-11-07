<?php
/**
 * Administra la creación automática de los Shortcodes.
 * 
 */
namespace Fw\Init\Shortcode;

use Fw\Core\Request\RequestShortcode;
use Fw\Init\Shortcode\Shortcodes;
use Fw\Config\Apps;
use Fw\Paths;

class ShortcodesManager
{
    /**
     * Valida y prepara los argumentos para la creación de los Shortcodes.
     *
     * @return void
     **/
    public static function initialize(string $pluginSlug) : void
    {
        # Ruta de los Shortcodes.
        $shortcodesPath = Apps::getConfig( $pluginSlug, 'paths' )
            ?->controllers
            ?->shortcodes;

        if ( !$shortcodesPath || !file_exists($shortcodesPath) ) {
            return;
        }

        # Folers.
        if ( !$dirs = array_diff(scandir($shortcodesPath), array('.', '..')) ) {
            return;
        }
        
        # Validación para determinar si se crea Shortcodes de la App.	
        $shortcode = false;
        foreach ($dirs as $directory) {
            $mainPath  = Paths::buildPath($shortcodesPath, $directory);
            if ( !$files = glob(Paths::buildPath($mainPath, '*.php')) ) {
                continue;
            }

            $shortcode = true;
            break;
        }

        if ( $shortcode ) {
            self::create( $pluginSlug );
        }
    }

    /**
     * Creación de los Shortcodes.
     * Ejemplo: [plugin_ejemplo Post=Controller@method param1='value1' param2='value2']
     *
     * @param string $pluginSlug Slug de la App.
     * @return void
     **/
    public static function create( $pluginSlug ) : void
    {
        $shortcodesPath = Apps::getConfig( $pluginSlug, 'config' )
            ->pluginSlug;

        # Creación de los Shortcodes.
        $tag = strToSlug( $pluginSlug, '_' );
        $tag = str_replace('-', '_', $tag);

        add_shortcode( $tag, [new RequestShortcode($pluginSlug), 'prepare'] );
    }

}
