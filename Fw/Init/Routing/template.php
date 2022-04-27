<?php
/**
 * Plantilla para renderizar URLs del WordPress Framework.
 *
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>

		<?php wp_footer(); ?>
	</body>
</html>
