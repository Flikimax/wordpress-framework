<?php
/**
 * Gestiona la solicitud Web para el usuario.
 * 
 */

namespace Fw\Core\Request;

use Fw\Core\Response\Response;
use Fw\Core\Exceptions\General;

class RequestWeb extends Request
{
    /**
     * Valida y ejecuta la solicitud.
     * 
     * @return string|null
     */
    public function send(): ?string
    {
        # Ejecución de la solicitud. 
        try {
            # Validaciones.
            if ( !$callback = $this->validations() ) {
                throw new General("Method: {$this->getMethod()}", 404);
            } else if ( is_string($callback) ) {
                return $callback;
            }

            $response = call_user_func($callback);
            if ( $response instanceof Response ) {
                $response->send($this->pluginPath);
            }
        } catch (General $e) {
            echo $e->getError();
        }
        
        return null;
    }

    /**
     * Validaciones previas a ejecutar la solicitud.
     *
     * @return array|string|null
     **/
    public function validations(): array|string|null
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
