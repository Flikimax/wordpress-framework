<?php
/**
 * Crea los shortcodes.
 * 
 */

namespace Fw\Init\Shortcode;

use Fw\Init\Request\RequestShortcode;
use Fw\Paths;

class Shortcodes 
{
    /**
     * Creación de los Shortcodes.
     *
     * @param string $namespace Namespace base de la aplicación.
     * @param array $shortcodes Shortcodes disponibles.
     * @return void
     **/
    public static function createShortcodes(string $namespace, array $shortcodes) : void
    {
        if ( !$shortcodes || !is_array($shortcodes) ) {
            return;
        }

        $pluginPath = $shortcodes['path'];
        unset($shortcodes['path']);

        # Se recorren los Shortcodes.
        foreach (array_keys($shortcodes) as $name) {
            $tag = strToSlug( spaceUpper("{$namespace}_") . spaceUpper($name), '_' );
            $shortcodeFiles = $shortcodes[$name];
            
            # Creación de los Shortcodes.
            add_shortcode($tag, [new RequestShortcode(
                $pluginPath,
                Paths::buildNamespacePath($namespace, 'Controllers', 'Shortcodes', $name),
                [$name => $shortcodeFiles],
            ), 'prepare']);
        }
    }
    
}
