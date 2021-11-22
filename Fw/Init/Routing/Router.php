<?php
/**
 * Gestiona las rutas utilizando la API de reescritura de WordPress.
 * 
 */

namespace Fw\Init\Routing;

class Router
{
    /** @var string $routeName Nombre de la ruta. */
    private string $routeName;
    /** @var array $routes Rutas a registrar. */
    private array $routes = array();
    /** @var bool $force Si Forzar ruta. */
    private bool $force;

    public function __construct(string $routeName, bool $force = false)
    {
        $this->routeName = $routeName;
        $this->force = $force;
    }

    /**
     * Añade una ruta al router.
     * Sobrescribe una ruta si comparte el mismo nombre que una ya registrada.
     *
     * @param string $name
     * @param Route  $route
     */
    public function addRoute(string $name, Route $route)
    {
        $this->routes[$name] = $route;
    }

    /**
     * Compila el enrutador (routeName) en reglas de reescritura de WordPress.
     * 
     * Crea las reglas de escritura para el uso de un controlador y métodos.
     * Registra las reglas de escritura en WordPress.
     * 
     */
    public function compile() : void
    {
        # Para el controlador
        add_rewrite_tag(
            "%{$this->routeName}%", 
            '(.+)'
        );
        # Para el uso de métodos por medio de URI
        add_rewrite_tag(
            "%method%", 
            '(.+)'
        );

        foreach ($this->routes as $name => $route) {
            $this->addRule($name, $route);
        }
    }

    /**
     * Añade una nueva regla de reescritura de WordPress para la ruta dada.
     *
     * @param string $name
     * @param Route  $route
     * @param string $priority
     */
    private function addRule(string $name, Route $route) : void
    {
        add_rewrite_rule(
            $this->generateRouteRegex($route), # Expresión regular para comparar la solicitud.
            "index.php?{$this->routeName}=$name", 
            $this->priority($route)
        );
    }

    /**
     * Añade una nueva regla de reescritura de WordPress para la ruta dada.
     * Habilita el uso de métodos por medio de URI.
     *
     * @param string $name
     * @param Route  $route
     * @param string $priority
     */
    public function addRuleEnableUri(string $name, Route $route) : void
    {
        add_rewrite_rule(
            $this->generateRouteRegex($route, '/([^/]*)'), 
            "index.php?{$this->routeName}=$name" . '&method=$matches[1]', 
            $this->priority($route)
        );
    }

    /**
     * Se define la prioridad de la ruta.
     *
     * @param Route $route
     * @return string
     **/
    public function priority(Route $route) : string
    {
        if ( $this->force && $route->force ) {
            return 'top';
        } else if ( !$this->force && $route->force ) {
            return 'top';
        } else if ( $this->force && is_null($route->force) ) {
            return 'top';
        }

        return 'bottom';
    }


    /**
     * Genera la regex para la API de reescritura de WordPress para la ruta dada.
     *
     * @param Route $route
     * @return string
     */
    private function generateRouteRegex(Route $route, string $matches = '') : string
    {
        return '^'. ltrim(trim($route->getPath()), '/') . "{$matches}$";
    }

    /**
     * Intenta encontrar una ruta en queryVariables. 
     *
     * @param array $query_variables
     * @return Route|WP_Error
     */
    public function match(array $queryVariables)
    {
        if (empty($queryVariables[$this->routeName])) {
            return new \WP_Error('missing_routeName');
        }

        $routeName = $queryVariables[$this->routeName];
        if (!isset($this->routes[$routeName])) {
            return new \WP_Error('route_not_found');
        }

        return $this->routes[$routeName];
    }

    /**
     * Borra todas las rutas de WordPress.
     *
     */
    public static function flush() : void
    {
        flush_rewrite_rules();
    }

    /**
     * Obtener si se debe forzar la ruta (conf global).
     *
     */
    public function getForce()
    {
        return $this->force;
    }

    /**
     * Obtener el routeName
     *
     */
    public function getRouteName()
    {
        return $this->routeName;
    }
}