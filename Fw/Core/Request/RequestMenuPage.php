<?php
/**
 *  Gestiona las solicitudes Menu Page para el usuario de administración.
 * 
 */

namespace Fw\Core\Request;

use Fw\Core\Response\Response;
use Fw\Core\Exceptions\General;

class RequestMenuPage extends Request
{
    /**
     * Se valida y ejecuta la solicitud para las (Sub) Menu Page.
     * 
     * @return string|null;
     */
    public function send() : ?string
    {
        # Ejecución de la solicitud. 
        try {
            # Validaciones.
            if ( !$callback = $this->validations() ) {
                throw new General("Method: {$this->getMethod()}", 404);
            }

            $response = call_user_func_array(
                $callback, 
                [
                    $this->params()
                ]
            );
            
            if ($response instanceof Response) {
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
     * @return null|array
     **/
    public function validations() : ?array
    {
        $controller = $this->getController();
        if ( isset($_GET['option']) && !empty($_GET['option']) ) {
            $this->method = $_GET['option'];
        }
        $method = $this->getMethod();
        

        if ( !self::methodExists($controller, $method, 'isPublic') ) {
            if (self::methodExists($controller, 'error404', 'isPublic')) {
                $method = 'error404';
            } else {
                if (WP_DEBUG) {
                    return null;
                }
                $method = $this->getMethod();
            }   
        }

        return [
            new $controller, 
            $method
        ];
    }

    /**
     * Parámetros a enviar a las funciones Menu Page.
     *
     * @return array
     **/
    public function params() : array
    {
        # Url Base de la Menu Page.
        $baseUrl = explode( '\\', $this->getController() );
        array_pop($baseUrl);
        $baseUrl = array_shift($baseUrl) . '-' . array_pop($baseUrl);
        $baseUrl = strToSlug( $baseUrl );

        return compact( 'baseUrl' );
    }

}
