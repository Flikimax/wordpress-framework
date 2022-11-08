<?php
/**
 * Recibe la solicitud y gestiona la respuesta para el usuario.
 *
 */

namespace Fw\Core\Request;

use ReflectionMethod;

abstract class Request implements RequestInterface
{
    /** @var string $pluginPath Ruta principal de la aplicación. */
    public string $pluginPath;
    
    /** @var string $controller Namespace del controlador. */
    protected string $controller;
    
    /** @var string $method Método a ejecutar por el controller. */
    protected string $method;
    
    public function __construct(string $pluginPath, string $controller = null, string $method = null)
    {
        $this->pluginPath = $pluginPath;
        $this->setProperty('controller', $controller);
        $this->setProperty('method', $method);
    }
    
    /**
     * Obtiene el controlador para la solicitud.
     *
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }
    
    /**
     * Obtiene el método para la solicitud.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    /**
     * Verifica si un método existe.
     * Comprueba su accesibilidad.
     *
     * isFinal, isPrivate, isProtected, isPublic, isStatic.
     *
     * @param string $controller
     * @param string $method
     * @param string|null $access Valores permitidos: isAbstract, isConstructor(), isDestructor(),
     *
     * @return bool
     **/
    public static function methodExists(string $controller, string $method, ?string $access = null): bool
    {
        $methodExists = false;
        
        if (method_exists($controller, $method)) {
            $methodExists = true;
            if ($access) {
                $reflection = new ReflectionMethod($controller, $method);
                if (!$reflection->$access()) {
                    $methodExists = false;
                }
            }
        }
        
        return $methodExists;
    }
    
    /**
     * Verifica si una propiedad existe.
     *
     * @param string $controller
     * @param string $property
     * @return null|bool
     **/
    public static function propertyExists(string $controller, string $property): ?bool
    {
        if (property_exists($controller, $property)) {
            return (bool)$controller::$$property;
        }
        
        return null;
    }
    
    /**
     * @param string $name
     * @param mixed $property
     * @return void
     */
    protected function setProperty(string $name, mixed $property): void
    {
        if ( $property ) {
            $this->$name = $property;
        }
    }
    
}
