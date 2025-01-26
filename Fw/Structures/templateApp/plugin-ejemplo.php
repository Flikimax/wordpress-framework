<?php
/*
@wordpress-plugin
# Data
* License: GPLv2 or later
*/

if ( ! require_once(__DIR__ . '/requirements.php') ) {
    return;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    include_once __DIR__ . '/vendor/autoload.php';
}

new Fw\Framework(__FILE__);
