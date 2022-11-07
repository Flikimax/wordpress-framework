<?php
/**
 * Gestiona los tipos de respuesta para el usuario.
 * 
 */

namespace Fw\Core\Response;

class Response  
{
    /** @var string $responseType Tipo de respuesta. */
    protected string $responseType; 
    /** @var string $data Data para el tipo de respuesta. */
    protected string $data; 
    /** @var mixes $args Argumentos para la respuesta. */
    protected $args; 
    /** @var string $pluginPath Ruta de la aplicación. */
    public string $pluginPath; 
    
    function __construct (string $responseType, $data, $args = array()) 
    {
        $this->responseType = $responseType;
        $this->data = $data;
        $this->args = $args;
    }

    /**
     * Dependiendo del tipo de respuesta requerida, se ejecuta.
     * 
     * @return void
     */
    public function send(string $pluginPath) : void
    {
        $this->pluginPath = $pluginPath;

        $response = 'send' . ucfirst($this->getResponseType());
        $this->$response();
    }

    /**
     * Comprueba si un argumento es de Tipo Response.
     *
     * @param mixes $arg Argumento a evaluar.
     * @return bool
     **/
    public function isResponse($arg) : bool
    {
        if ( $arg instanceof Response ) {
            return true;
        }

        return false;
    }

    /**
     * Ciclo de validación y retorno de respuestas para respuestas anidadas.
     *
     * @param mixes $arg
     * @param string $pluginPath
     * @return array
     **/
    public function forArgs($args, string $pluginPath) : array
    {
        if ( !$args ) {
            return array();
        }

        foreach ($args as $key => $arg) {
            if ( $this->isResponse($arg) ) {
                $args[$key] = $arg->response($pluginPath);
            }
        }

        return $args;
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
}
