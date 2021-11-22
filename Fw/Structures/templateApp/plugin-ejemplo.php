<?php
/*
@wordpress-plugin
# Data
* License: GPLv2 or later
*/

if (!defined('ABSPATH')) {
	exit;
} 

if (in_array('wp-fw/wp-framework.php', get_option('active_plugins'))) { 
    require_once (WP_PLUGIN_DIR . '/wp-fw/wp-framework.php');

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




