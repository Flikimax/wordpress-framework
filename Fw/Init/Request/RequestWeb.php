<?php
/**
 * Gestiona la solicitud Web para el usuario.
 * 
 */

namespace Fw\Init\Request; 

use Fw\Init\Request\Request;
use Fw\Init\Request\RequestInterface;
use Fw\Init\Response\Response;

class RequestWeb extends Request implements RequestInterface
{
    /**
     * Valida y ejecuta la solicitud.
     * 
     * @return string|null
     */
    public function send()
    {
        # Ejecución de la solicitud. 
        try {
            # Validaciones.
            if ( !$callback = $this->validations() ) {
                throw new \Fw\Init\Exceptions\General("Method: {$this->getMethod()}", 404);
            } else if ( is_string($callback) ) {
                return $callback;
            }

            get_header();
            $response = call_user_func($callback);

            if ($response instanceof Response) {
                $response->send($this->pluginPath);
            }
            get_footer();
        } catch (\Fw\Init\Exceptions\General $e) {
            echo $e->getError();
        }
        
        return null;
    }

    /**
     * Validaciones previas a ejecutar la solicitud.
     *
     * @return string|null|array
     * @throws General Method not found.
     **/
    public function validations() 
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
            } else if (WP_DEBUG) {
                return null;
            } else {
                wp_redirect('/');
            }
        }

        return [
            new $controller, 
            $method
        ];
    }

}
