<?php
/*
@wordpress-plugin
# Data
* License: GPLv2 or later
*/

if ( !defined('ABSPATH') ) {
	exit;
}

# Se valida que el plugin este activado y que el archivo principal del Wp Framework exista.
$fwPath = 'wordpress-framework/wp-framework.php';
if ( in_array($fwPath, get_option('active_plugins')) && file_exists(WP_PLUGIN_DIR . "/{$fwPath}") ) { 
    require_once (WP_PLUGIN_DIR . "/{$fwPath}");

    if ( file_exists(__DIR__ . '/autoload/autoload.php') ) {
        try {
            include_once __DIR__ . '/autoload/autoload.php';
        } catch (\Throwable $th) {
            Fw\Structures\BuildStructures::remove(__DIR__ . '/autoload/');
            if (WP_DEBUG) {
                echo "<strong>Details:</strong> {$th->getMessage()}. <br>";
            }
        }
    }
    
    new Fw\Framework(__FILE__);
}
