<?php
/**
 * Obtiene las rutas a crear.
 * Inicia el proceso de creación de rutas.
 * 
 */

namespace Fw\Init\Routing;

use Fw\Paths;
use Fw\Init\Routing\Route;
use Fw\Init\Routing\Router;
use Fw\Init\Request\Request;

class RoutingManager  
{
    /**
     * Se inicializa y crea las rutas.
     *
     * @param string $routerName nombre del conjunto de rutas publicas.
     * @param string $path Path de los controladores públicos.
     * @param array $args
     * @return void
     **/
    public static function initialize(string $routerName, string $path, array $args = array()) : void
    {
        if ( !file_exists($path) ) {
            return;
        }

        $router = new Router($routerName, $args['force']);
        $router->pluginPath = Paths::trimPath($path, 2);
        $routes = self::prepareRoutes($routerName, $path);
        RoutingProcessor::init($router, $routes);
    }

    /**
     * Crea los Routes.
     *
     * @param string $routerName
     * @param string $path 
     * @return $routes
     **/
    public static function prepareRoutes(string $routerName, string $path) : array
    {
        $routes = array();

        foreach (glob(Paths::buildPath($path, '*.php')) as $controller) {
            $controllerName = basename($controller, '.php');
            $controller = "$routerName\Controllers\Web\\$controllerName";

            $route = str_replace('Controller', '', $controllerName);
            $routeKey = strtolower($route); 

            # Creación del objecto de ruta.
            $routes[$routeKey] = new Route(
                self::routeUrl($controller, $route),
                $controller
            );
            # Propiedades adicionales
            $routes[$routeKey]->force = Request::propertyExists($controller, 'routeForce');
            $routes[$routeKey]->enableUri = (bool) Request::propertyExists($controller, 'enableUri');
        }

        return $routes;
    }

    /**
     * Retorna una url lista para usar en un Route.
     *
     * @param string $controller
     * @param string $route
     * @return string
     **/
    private static function routeUrl(string $controller, string $route) : string
    {
        if ( property_exists($controller, 'routeUrl') ) {
            $route = $controller::$routeUrl;
        } else {
            $route = preg_replace( '/[A-Z]/', '-$0',  $route);
            $route = strtolower( ltrim($route, '-') );
        }

        return '/' . trim($route, '/');
    }
}


