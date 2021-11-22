<?php
/**
 * Obtiene y valida las Menu Page y las Sub Menu Pages para su creación.
 * 
 */
namespace Fw\Init\MenuPage;

use Fw\Paths;
use Fw\Init\MenuPage\MenuPages;
use Fw\Init\Request\Request;

class MenuPagesManager  
{
    /** @var array $args Argumentos de las Menu Page y las Sub Menu Pages. */
    protected array $args;

    /** @var string $namespace Namespace base de la aplicación. */
    protected string $namespace;
    /** @var string $path Ruta principal de las Menu Pages. */
    protected string $path;

    public function __construct(string $namespace, string $path) {
        $this->namespace = $namespace;
        $this->path = $path;
    }

    /**
     * Se envian los argumentos para la creación de las (Sub) Menu Page.
     * 
     * @return void
     **/
    public function createMenuPages() : void
    {
        MenuPages::createMenuPages($this->args);
    }

    /**
     * Prepara el proceso de creación.
     *
     * @return void
     **/
    public function prepare() : void
    {
        if ( !file_exists($this->path) ) {
            return;
        }

        # Folers
        if ( !$dirs = array_diff(scandir($this->path), array('.', '..')) ) {
            return;
        }
        
        $menuPages = array(
            'path' => Paths::findPluginPath($this->path)
        );
        foreach ($dirs as $directory) {
            $mainPath = Paths::buildPath($this->path, $directory);

            if ( !$files = glob(Paths::buildPath($mainPath, '*.php')) ) {
                continue;
            }

            # MENU PAGE PRINCIPAL
            $menuPageKey = array_search(Paths::buildPath($mainPath, "{$directory}Controller.php"), $files); 
            if ( $menuPageKey ) {
                $menuPage = $files[$menuPageKey];
                unset($files[$menuPageKey]);
            } else {
                $menuPage = $files[0];
                unset($files[0]);
            }

            # Menu Page
            $menuPages[$directory] = $this->prepareMenuPage(self::menuPageNamespace(
                $this->namespace,
                $directory,
                $menuPage
            ));

            # Sub Menu Page
            foreach ($files as $file) {
                $menuPages[$directory]['subMenuPages'][] = $this->prepareMenuPage(
                    self::menuPageNamespace(
                        $this->namespace,
                        $directory,
                        $file
                    ),
                true);
            }
        }
        $this->args = $menuPages;

        $this->createMenuPages();
    }


    /**
     * Retorna un array con los datos necesarios para crear una (Sub) Menu Page.
     *
     * @param string $class
     * @param bool $isSubMenuPage
     * @return array
     **/
    public function prepareMenuPage(string $class, bool $isSubMenuPage = false) : array
    {
        $controllerName = str_replace('\\', '/', $class);
        $controllerName = basename($controllerName);
        $controllerName = str_replace('Controller', '', $controllerName);
        
        # Page Title
        $menuPage['pageTitle'] = Request::propertyExists($class, 'pageTitle') ? $class::$pageTitle : spaceUpper($controllerName);

        # Menu Title
        $menuPage['menuTitle'] = Request::propertyExists($class, 'menuTitle') ? $class::$menuTitle : spaceUpper($controllerName);

        # Capability
        $menuPage['capability'] = Request::propertyExists($class, 'capability') ? $class::$capability : 'install_plugins';

        # Slug
        $menuPage['menuSlug'] = Request::propertyExists($class, 'menuSlug') ? $class::$menuSlug : $controllerName;
        $menuPage['menuSlug'] = strToSlug($menuPage['menuSlug']);

        # callable
        $menuPage['callable'] = [
            'controller' => $class,
            'method' => Request::propertyExists($class, 'callable') ? $class::$callable : 'index'
        ];
        
        if ( !$isSubMenuPage ) {
            # Icon
            $menuPage['icon'] = Request::propertyExists($class, 'icon') ? $class::$icon : 'dashicons-schedule';
        }     

        # Position
        $menuPage['position'] = Request::propertyExists($class, 'position') ? $class::$position : 4;

        return $menuPage;
    }

    /**
     * Crea y retorna el namespace de una Menu Page.
     *
     * @param string $namespace namespace base de la aplicación.
     * @param string $directory
     * @param string $filePath Ruta del controlador.
     * @return string
     **/
    public static function menuPageNamespace(string $namespace, string $directory, string $filePath) : string
    {
        $controllerName = basename($filePath, '.php');
        return "$namespace\\Controllers\MenuPages\\$directory\\$controllerName";
    }
}
