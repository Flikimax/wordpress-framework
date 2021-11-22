<?php
/*
@wordpress-plugin
Plugin Name:    		Framework Wp
Plugin URI: 			https://github.com/Flikimax/wp-framework
Description:    		Framework mvc (modelo-vista-controlador) que ayuda a disminuir el tiempo de desarrollo de sistemas.
Version:        		0.3
Author: 				Flikimax
Author URI: 			https://flikimax.com
License: GPLv2 or later
*/

if (!defined('ABSPATH')) {
	exit;
}

define('WPFW_NAME', 'Framework Wp');
define('WPFW_PATH', __DIR__);
define('WPFW_VERSION', '20210913');

# SE CARGA EL AUTOLOAD DEL FRAMEWORK
require __DIR__ . '/vendor/autoload.php';

new Fw\Framework(__FILE__, [
	'mode' => 'production',
	'autoload' => false
]);



