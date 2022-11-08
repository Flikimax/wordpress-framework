<?php
/**
 * Gestiona los tipos de respuesta para el usuario.
 * 
 */

namespace Fw\Core\Response;

use Fw\Core\Exceptions\General;

class ResponseView extends Response
{
    /**
     * Respuesta de tipo View.
     *
     * @return void
     * @throws General
     */
    public function sendView(): void
    {
        $viewName = $this->getData();
        $args = $this->getArgs();
        $parameters = array(); 

        if ( is_array($args) ) {
            $parameters = $this->forArgs($args, $this->pluginPath);
        } else if ( $this->isResponse($args) ) {
            $parameters['arg'] = $args->response($args, $this->pluginPath);
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
     * Retorna su vista y sus variables extras.
     *
     * @param string $pluginPath
     * @return string|null
     *
     * @throws General
     */
    public function response(string $pluginPath): ?string
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
