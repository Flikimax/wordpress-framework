<?php
/**
 * Gestiona los tipos de respuesta para el usuario.
 * 
 */

namespace Fw\Core\Response;

abstract class Response implements ResponseInterface
{
    /** @var string $responseType Tipo de respuesta. */
    protected string $responseType;
    
    /** @var string $data Data para el tipo de respuesta. */
    protected string $data;
    
    /** @var mixed $args Argumentos para la respuesta. */
    protected mixed $args;
    
    /** @var string $pluginPath Ruta de la aplicación. */
    public string $pluginPath; 
    
    function __construct (string $responseType, string $data, mixed $args = array())
    {
        $this->responseType = $responseType;
        $this->data = $data;
        $this->args = $args;
    }

    /**
     * Dependiendo del tipo de respuesta requerida, se ejecuta.
     *
     * @param string $pluginPath
     * @return void
     */
    public function send(string $pluginPath): void
    {
        $this->pluginPath = $pluginPath;

        $response = 'send' . ucfirst($this->getResponseType());
        $this->$response();
    }

    /**
     * Comprueba si un argumento es de Tipo Response.
     *
     * @param mixed $arg Argumento a evaluar.
     * @return bool
     **/
    public function isResponse(mixed $arg): bool
    {
        if ( $arg instanceof Response ) {
            return true;
        }

        return false;
    }
    
    /**
     * Ciclo de validación y retorno de respuestas para respuestas anidadas.
     *
     * @param mixed $args
     * @param string $pluginPath
     * @return array
     */
    public function forArgs(mixed $args, string $pluginPath): array
    {
        if ( ! $args ) {
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
    public function getResponseType(): string
    {
        return $this->responseType;
    }

    /**
     * Obtiene el dato para la respuesta.
     * 
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Obtiene los argumentos para la respuesta.
     * 
     * @return mixed
     */
    public function getArgs(): mixed
    {
        return $this->args;
    }
}
