<?php
/**
 *  Gestiona las solicitud Menu Page para el usuario de administración.
 * 
 */

namespace Fw\Init\Request; 

use Fw\Init\Request\Request;
use Fw\Init\Request\RequestInterface;
use Fw\Init\Response\Response;

class RequestMenuPage extends Request implements RequestInterface
{
    /**
     * Valida y ejecuta la solicitud para las (Sub) Menu Page.
     * 
     * @return string|null;
     */
    public function send() : ?string
    {
        # Validaciones.
        if ( !$method = $this->validations() ) {
            return null;
        }

        # Ejecuciónde la solicitud. 
        try {
            $controller = $this->getController();
            $response = call_user_func([
                new $controller,
                $method
            ]);

            if ($response instanceof Response) {
                $response->send($this->pluginPath);
            } 
        } catch (\Exception $e) {
            if (WP_DEBUG) {
                echo "<strong>Details:</strong> {$e->getMessage()}. <br>";
            }
        }
        
        return null;
    }

    /**
     * Validaciones previas a ejecutar la solicitud.
     *
     * @return string
     **/
    public function validations() : string
    {
        $controller = $this->getController();

        if ( isset($_GET['option']) && !empty($_GET['option']) ) {
            $method = $_GET['option'];
        } else {
            $method = $this->getMethod();
        }

        if ( !self::methodExists($controller, $method, 'isPublic') ) {
            if (self::methodExists($controller, 'error404', 'isPublic')) {
                $method = 'error404';
            } else {
                if (WP_DEBUG) {
                    echo "<strong>Details:</strong> The {$method} method does not Exist. <br>";
                }
                return $this->getMethod();
            }
        }

        return $method;
    }


}
