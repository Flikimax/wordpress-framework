<?php
/**
 * Gestiona los tipos de respuesta para el usuario.
 * 
 */

namespace Fw;

use Fw\Paths;

class Response  
{
    protected string $responseType; 
    protected $data; 
    protected $args; 
    public $pluginPath; 
    
    function __construct (string $responseType, $data, $args = null) 
    {
        $this->responseType = $responseType;
        $this->data = $data;
        $this->args = $args;
    }

    /**
     * Dependiendo del tipo de respuesta requerida, se ejecuta.
     * 
     * @return mixes
     */
    public function send(string $pluginPath)
    {
        $this->pluginPath = $pluginPath;

        $response = 'send' . ucfirst($this->getResponseType());
        $this->$response();
    }

    /**
     * Respuesta de tipo View.
     * 
     * @return mixes
     */
    public function sendView()
    {
        $viewName = $this->getData();
        $args = $this->getArgs();

        if ( is_array($args) ) {
            foreach ($args as $key => $arg) {
                if ($view = $this->validateViewTypeResponse($arg)) {
                    $args[$key] = $view;
                }
            }
        } else if ( is_string($args) ) {
            if ($view = $this->validateViewTypeResponse($args)) {
                $args[$key] = $view;
            }
        }

        if ( $viewPath = viewPath($this->pluginPath, $viewName) ) {
            extract($args);
            require_once($viewPath);
        } else {
            if (WP_DEBUG) {
                echo "Details: ";
            }
        }
    }

    /**
     * Obtiene el tipo de respuesta.
     * 
     * @return string
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * Obtiene el dato para la respuesta.
     * 
     * @return mixes
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Obtiene los argumentos para la respuesta.
     * 
     * @return mixes
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Valida si un argumento es de tipo Response View.
     *
     * @param mixes $arg
     * @return string|null
     **/
    private function validateViewTypeResponse($arg) : ?string
    {
        if ($arg instanceof Response && $arg->getResponseType() === 'view') {
            if ( $viewPath = viewPath($this->pluginPath, $arg->getData()) ) {
                return file_get_contents($viewPath);
            }
        }

        return null;
    }

}
