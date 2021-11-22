<?php
/**
 * Ruta individual con sus respectivos parametros.
 *
 */

namespace Fw\Init\Routing;

class Route 
{
    # URL
    private string $path;
    /** @var string $controllerName Nombre de la clase o Namespace. */
    private string $controllerName;
    /** @var null|bool $force Forzar la ruta. */
    public ?bool $force = null;
    /** @var bool $enableUri Si usará los métodos URI. */
    public bool $enableUri = false;
    
    public function __construct(string $path, string $controllerName)
    {
        $this->path = $path;
        $this->controllerName = $controllerName;
    }

    /**
     * Obtener la URL de la ruta.
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }
    
    /**
     * Obtener el nombre del controlador a cargar.
     * @return string
     */
    public function getControllerName() : string
    {
        return $this->controllerName;
    }

    /**
     * Verifica si la ruta tiene un controlador asignado.
     * @return bool
     */
    public function hasController() : bool
    {
        return !empty($this->controllerName);
    }
}