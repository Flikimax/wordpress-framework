<?php
/**
 * Recibe la solicitud y gestiona la respuesta para el usuario.
 * 
 */

namespace Fw\Request;

use Fw\Response;

class Request  
{
    /** @var string $controller Namespace del controlador. */
    protected $controller;
    /** @var string $method Método a ejecutar por el controller. */
    protected $method;
    /** @var string $pluginPath Ruta principal de la aplicación. */
    public string $pluginPath;

    public function __construct(string $pluginPath, string $controller, string $method) 
    {
        $this->pluginPath = $pluginPath;
        $this->controller = $controller;
        $this->method = $method;
    }

    /**
     *Valida y ejecuta la solicitud.
     * 
     * @return mixes
     */
    public function send()
    {
        /**
         * TODO: CREAR SOLICITUD GENERAL PARA: 
         * MENU PAGE
         * SHORTCODES
         * 
         */
    }

    /**
     * Obtiene el controlador para la solicitud.
     * 
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Obtiene el método para la solicitud.
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Verifica si un metodo existe.
     * Comprueba su accesibilidad.
     *
     * @param string $controller
     * @param string $method
     * @param string $access Valores permitidos: isAbstract, isConstructor(), isDestructor(), 
     * isFinal, isPrivate, isProtected, isPublic, isStatic.
     * 
     * @return bool
     **/
    public static function methodExists(string $controller, string $method, ?string $access = null) : bool
    {
        $methodExists = false;

        if ( method_exists($controller, $method) ) {
            $methodExists = true;
            if ( $access ) {
                $reflection = new \ReflectionMethod($controller, $method);
                if ( !$reflection->$access() ) {
                    $methodExists = false;
                }
            } 
        }

        return $methodExists;
    }

}
