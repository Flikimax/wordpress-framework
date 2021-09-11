<?php
/**
 * Gestiona la solicitud Web para el usuario.
 * 
 */

namespace Fw\Request;

use Fw\Request\Request;
use Fw\Response;

class RequestWeb extends Request  
{
    /**
     *Valida y ejecuta la solicitud.
     * 
     * @return string|null
     */
    public function send()
    {
        $controller = $this->getController();
        $method = $this->getMethod();

        # Validaciones.
        if ( !self::methodExists($controller, $method, 'isPublic') ) {
            if (self::methodExists($controller, 'error404', 'isPublic')) {
                $method = 'error404';
            } else if ( !empty(get_404_template()) ) {
                global $wp_query;
                $wp_query->is_404 = true;
                return get_404_template();
            } else {
                wp_redirect('/');
            }
        }

        # Ejecuciónde la solicitud. 
        try {
            get_header();

            $response = call_user_func([
                new $controller,
                $method
            ]);

            if ($response instanceof Response) {
                $response->send($this->pluginPath);
            }

            get_footer();
        } catch (\Exception $e) {
            if (WP_DEBUG) {
                echo "Details: {$e->getMessage()}";
            }
        }
        
        return null;
    }

}
