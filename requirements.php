<?php
# Requisitos para el funcionamiento del Framework.
if ( !defined('ABSPATH') ) {
	exit;
}

if ( version_compare(PHP_VERSION, '8.0.2', '<') ) {
	require __DIR__ . '/Fw/helpers/AdminNotice.php';
	AdminNotice::generalAdminNotice(
		'WordPress Framework',
		'Las dependencias del plugin <strong>WordPress Framework</strong> requieren una versión de PHP igual o superior a la "8.0.2". <br>Versión actual de PHP: <strong>' . PHP_VERSION . '</strong>.',
		'warning',
	);
	return;
}

if ( !file_exists(__DIR__ . '/vendor/autoload.php') ) {
	return;
}
