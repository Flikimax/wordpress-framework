<?php
/**
 * Gestiona las solicitud y la creación de Shortcode.
 * 
 */

namespace Fw\Init\Request; 

use Fw\Init\Request\Request;
use Fw\Init\Request\RequestInterface;
use Fw\Init\Response\Response;
use Fw\Config\Apps;
use Fw\Paths; 

class RequestShortcode extends Request implements RequestInterface
{
    /** @var object $instance Instancia de la App actual. */
    private object $instance;

    /** @var string $shortcode Recreación del Shortcode a ejecutar. */
    private string $shortcode;

    /** @var string $category Categoría a la que pertenece el controlador a ejecutar. */
    private string $category;

    /** @var string $shortcodeTag Shortcode tag. */
    private string $shortcodeTag;

    /** @var array $params Controlador a ejecutar. */
    private array $params;

    /** @var array $pluginSlug Slug de la App. */
    public function __construct(string $pluginSlug) 
    {
        $this->instance = Apps::getApp( $pluginSlug );
    }

    /**
     * Valida y prepara los datos.
     * Ejecuta el Request.
     * 
     * @param array $attrs Array de atributos del Shortcode.
     * @param string $content Contenido del Shortcode o nulo si no se establece.
     * @param string $shortcodeTag Shortcode tag.
     * Puedes obtener mas información: https://developer.wordpress.org/reference/functions/add_shortcode/#parameters
     * 
     * @return string
     **/
    public function prepare(array $attrs = array(), string $content = '', string $shortcodeTag = '')
    {
        try {
            # Se recrea el Shortcode con los atributos a ejecutar.
            $this->shortcode = "[{$shortcodeTag} ";
            foreach ($attrs as $key => $value) {
                $this->shortcode.= "{$key}='{$value}' ";
            }
            $this->shortcode .= ']';

            $this->category = sanitize_text_field( key($attrs) );
            $action = sanitize_text_field( array_shift($attrs) );

            if ( empty( $action ) ) {
                throw new \Fw\Init\Exceptions\General(
                    "<br>En el Shortcode: {$this->shortcode}<br>" . 'Se requiere el controller. <a target="_blank" href="https://flikimax.notion.site/95f7fde081684487b248f29a9d464c7c?v=58d41bc9f8864ff895c6e8d2e27ab245">Documentation</a> <br>', 
                    404
                );
            }

            $action = explode('@', $action);
            
            # Se obtienen los archivos de la categoria del Shortcode.
            $path = Paths::buildPath(
                $this->instance->paths->controllers->shortcodes, 
                $this->category,
                '*.php'
            );
            $this->files = glob( $path );

            $this->shortcodeTag = $shortcodeTag;
            $this->controller = Paths::buildNamespacePath( 
                $this->instance->config->namespace,
                'Controllers',
                'Shortcodes',
                $this->category,
                $action[0]
            );

            if ( class_exists($this->controller . 'Controller') ) {
                $this->controller = $this->controller . 'Controller';
            } else if ( !class_exists($this->controller) ) {
                throw new \Fw\Init\Exceptions\General("En el Shortcode: {$this->shortcode}<br>El Controller: {$this->controller} no fue encontrado.", 404);
            }

            $this->method = ( isset( $action[1] ) && !empty( $action[1] ) ) ? $action[1] : 'index';
            $this->params = $attrs;

            ob_start();
            $this->send();
            return ob_get_clean();
        } catch (\Exception $e) {
            if ( WP_DEBUG ) {
                return $e->getMessage();
            }
        }
    }

    /**
     * Valida y ejecuta la solicitud para los Shortcodes.
     * 
     * @return string|null;
     * @throws \Fw\Init\Exceptions\General
     */
    public function send() : ?string
    {
        # Ejecución de la solicitud. 
        try {
            # Se valida que el Shortcode tenga permiso para ejecutar el archivo de clase.
            if ( !$this->isAllowedFile() ) {
                throw new \Fw\Init\Exceptions\General("El Shortcode <strong>{$this->shortcode}</strong> no puede acceder al controlador: <strong>{$this->getController()}</strong>.", 404);
            }

            # Validaciones método.
            if ( !$callback = $this->validations() ) {
                throw new \Fw\Init\Exceptions\General("Method: {$this->getMethod()}", 404);
            }
            
            $response = call_user_func_array(
                $callback, 
                [$this->params]
            );

            if ($response instanceof Response) {
                $response->send( $this->instance->paths->app );
            }
        } catch ( \Fw\Init\Exceptions\General $e ) {
            echo $e->getError();
        }
        
        return null;
    }

    /**
     * Validaciones previas a ejecutar la solicitud.
     *
     * @return null|array
     **/
    public function validations() : ?array
    {
        $controller = $this->getController();
        $method = $this->getMethod();

        # Validación del Método.
        if ( !empty($method) ) {
            $method = $this->getMethod();
        } else {
            $method = 'index';
        }

        if ( !self::methodExists($controller, $method, 'isPublic') ) {
            if (self::methodExists($controller, 'error404', 'isPublic')) {
                $method = 'error404';
            } else {
                return null;
            }
        }

        return [
            new $controller($this->params), 
            $method
        ];
    }

    /**
     * Se valida que el shortcode pueda ejecutar el archivo.
     *
     * @return bool
     **/
    public function isAllowedFile() : bool
    {
        $controllerName = basename( Paths::parsePath($this->getController()) );
        
        $controllerPath = Paths::parsePath(
            Paths::buildPath(
                $this->instance->paths->controllers->shortcodes,
                $this->category,
                "{$controllerName}.php"
            )
        ); 

        if ( in_array($controllerPath, $this->files) ) {
            return true;
        }

        return false;
    }
}
