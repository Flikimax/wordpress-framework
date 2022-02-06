<?php
/**
 * Gestiona las solicitud Menu Page para el usuario de administración.
 * 
 */

namespace Fw\Init\Request; 

use Fw\Init\Request\Request;
use Fw\Init\Request\RequestInterface;
use Fw\Init\Response\Response;
use Fw\Paths; 

class RequestShortcode extends Request implements RequestInterface
{
    /** @var string $namespace Namespace del Shortcode. */
    protected string $namespace;
    /** @var array $files Ruta de los archivos de controlador. */
    protected array $files;

    public function __construct(string $pluginPath, string $namespace, array $files) 
    {
        $this->pluginPath = $pluginPath;
        $this->namespace = $namespace;
        $this->files = $files;
    }

    /**
     * Valida y prepara los datos.
     * Ejecuta el Request.
     * 
     * @param array $attrs Array de atributos del Shortcode.
     * @param string|null $content Contenido del Shortcode o nulo si no se establece.
     * Puedes obtener mas información: https://developer.wordpress.org/reference/functions/add_shortcode/#parameters
     * 
     * @return string
     **/
    public function prepare($attrs = array(), $content, $tag)
    {
        try {
            if ( !array_key_exists('controller', $attrs) || empty($attrs['controller']) ) {
                throw new \Exception('Se requiere el atributo: controller.');
            }

            # Se obtienen los datos.
            $this->tag = $tag;
            $this->controller = Paths::buildNamespacePath($this->namespace, $attrs['controller']);
            $this->method = ( !empty($attrs['method']) ) ? $attrs['method'] : 'index';
            unset( $attrs['controller'], $attrs['method'] );
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
     * @throws General
     */
    public function send() : ?string
    {
        # Ejecución de la solicitud. 
        try {
            # Se valida que el Shortcode tenga permiso para ejecutar el archivo de clase.
            if ( !$this->isAllowedFile() ) {
                throw new \Fw\Init\Exceptions\General("El Shortcode <strong>{$this->tag}</strong> no puede acceder al controlador: <strong>{$this->getController()}</strong>.", 404);
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
                $response->send($this->pluginPath);
            }
        } catch (\Fw\Init\Exceptions\General $e) {
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
        $shortcodeName = basename( Paths::parsePath($this->namespace) );
        $controllerName = basename( Paths::parsePath($this->getController()) );
        
        $ControllerPath = Paths::buildPath($this->pluginPath, 'Controllers', 'Shortcodes', $shortcodeName, "{$controllerName}.php");
        $files = $this->files[$shortcodeName];

        # Se verifica que el Shortcode pueda acceder al archivo de clase del controlador.
        if ( in_array($ControllerPath, $files) ) {
            return true;
        }

        return false;
    }
}
