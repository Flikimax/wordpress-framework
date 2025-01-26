<?php
# Requerimientos para WordPress Framework.
if ( !defined('ABSPATH') ) {
	exit;
}

/**
 * Validaciones.
 *
 * Que la versión de PHP sea mayor o igual a 8.0.2.
 * Que el plugin este activado.
 * Que el archivo principal del Wp Framework exista.
 * Que el autoload del framework exista.
 * 
 **/
$fwSlug = 'wordpress-framework';
$fwPathFile = "{$fwSlug}/wp-framework.php";
if ( 
    version_compare(PHP_VERSION, '8.0.2', '<') ||
    !in_array($fwPathFile, get_option('active_plugins')) || 
    !file_exists(WP_PLUGIN_DIR . "/{$fwPathFile}")
) {
    return;
}

require_once (WP_PLUGIN_DIR . "/{$fwPathFile}");
