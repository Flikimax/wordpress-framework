<?php
/**
 * Clase para mostrar mensajes de adminitración.
 * 
 */

class AdminNotice 
{
    public static $types = [
        'error',
        'warning',
        'success',
        'info',
    ];

    /**
     * Muestra un mensaje en la página de administración.
     *
     * @param string $title 
     * @param string $content 
     * @param string $type 
     * @param bool   $optionally 
     * @return void
     **/
    public static function generalAdminNotice($title, $content, $type = 'info', $optionally = false) : void
    {
        add_action('admin_notices', function () use ($title, $content, $type, $optionally) {
            $title = $title ?? '';
            $content = $content ?? '';
            $type = ( in_array($type, self::$types) ) ? $type : 'info';
            $optionally = ($optionally) ? 'is-dismissible' : '';

            // global $pagenow;
            // if ( $pagenow !== 'plugins.php' && $pagenow !== 'options-general.php' ) {
            //     return;
            // } ?> 

            <div class="notice notice-<?php echo $type; ?> <?php echo $optionally; ?>">
                <h1><?php echo $title; ?></h1>
                <p><?php _e( $content, 'wp-framework' ); ?></p>
            </div> <?php
        });
    }

}

