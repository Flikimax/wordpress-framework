<?php
/**
 * Gestiona los tipos de respuesta para el usuario.
 * 
 */

namespace Fw\Core\Response;

use Fw\Core\Response\Response;
use Fw\Core\Response\ResponseInterface;

class ResponseView extends Response implements ResponseInterface
{
    /**
     * Respuesta de tipo View.
     * 
     * @return mixes
     */
    public function sendView()
    {
        $viewName = $this->getData();
        $args = $this->getArgs();
        $parameters = array(); 

        if ( is_array($args) ) {
            $parameters = $this->forArgs($args, $this->pluginPath);
        } else if ( $this->isResponse($args) ) {
            $parameters['arg'] = $args->response($args, $pluginPath);
        } else {
            $parameters['arg'] = $args;
        }

        if ( $viewPath = viewPath($this->pluginPath, $viewName) ) {
            extract($parameters);
            require($viewPath);
        }
    }

    /**
     * Response View.
     * Retorna su vista y sus varaibles extraidas.
     *
     * @param string $pluginPath
     * @return string|null
     **/
    public function response(string $pluginPath)
    {
        if ( $viewPath = viewPath($pluginPath, $this->getData()) ) {
            $args = $this->forArgs( $this->getArgs(), $pluginPath );

            ob_start();
            extract($args);
            require $viewPath;
            return ob_get_clean(); 
        }

        return null;
    }
}
