<?php
/**
 * Se encarga de la interacción entre el sistema de enrutamiento y el resto de Wordpress
 *
 */

namespace Fw\Init\Routing;

use Fw\Core\Request\Request;
use Fw\Core\Request\RequestWeb;

class RoutingProcessor
{
    /** @var Route $matchedRoute La ruta coincidente encontrada por el router. */
    private $matchedRoute;
    /** @var Router $router */
    private $router;
    /** @var Route[] $routes Las rutas que queremos registrar en WordPress. */
    private $routes;

    public function __construct(Router $router, array $routes = array())
    {
        $this->router = $router;
        $this->routes = $routes;
    }

    /**
     * Inicializar actions necesarios.
     *
     * @param Router  $router
     * @param Route[] $routes
     */
    public static function init(Router $router, array $routes = array())
    {
        $self = new self($router, $routes);

        add_action('init', array($self, 'registerRoutes'));
        add_action('parse_request', array($self, 'matchRequest'));
        add_action('template_include', array($self, 'loadRouteController'));
    }

    /**
     * Registrar todas las rutas.
     * 
     */
    public function registerRoutes()
    {
        foreach ($this->routes as $name => $route) {
            if ( $route->enableUri ) {
                $this->router->addRuleEnableUri($name, $route);
            }
            $this->router->addRoute($name, $route);
        }
        
        $this->router->compile();  
        
        $routesHash = md5(serialize($this->routes));
        # Se validan cambios en las rutas
        if ($routesHash != get_option($this->router->getRouteName() . '_routesHash')) {
            flush_rewrite_rules();
            update_option($this->router->getRouteName() . '_routesHash', $routesHash);
        }
    }

    /**
     * Intenta hacer coincidir la solicitud actual con una ruta.
     *
     * @param WP $environment
     */
    public function matchRequest(\WP $environment)
    {
        $matchedRoute = $this->router->match($environment->query_vars);

        if ($matchedRoute instanceof Route) {
            $this->matchedRoute = $matchedRoute;
        }

        if ($matchedRoute instanceof \WP_Error && in_array('route_not_found', $matchedRoute->get_error_codes())) {
            wp_die($matchedRoute, 'Route Not Found', array('response' => 404));
        }
    }


    /**
     * Comprueba si se ha encontrado a una ruta. 
     * Si hay una, carga el controller para la ruta.
     * Ejecuta el método enviado, el index(), el método error404() o la platilla 404 del tema respectivamente.
     *
     * @param string $template
     * @return string
     */
    public function loadRouteController(string $template)
    {
        if (!$this->matchedRoute instanceof Route || !$this->matchedRoute->hasController()) {
            return $template;
        }

        $controller = $this->matchedRoute->getControllerName();
        if ( class_exists($controller) ) {
            $method = 'index';
            if ( (bool) get_query_var('method') ) {
                $method = get_query_var('method');
            }

            # Ejecución del RequestWeb
            $request = new RequestWeb(
                $this->router->pluginPath,
                $controller,
                $method
            );
            $request->send();

            return \Fw\Paths::buildPath( __DIR__, 'template.php' );
        }

        return $template;
    }

}