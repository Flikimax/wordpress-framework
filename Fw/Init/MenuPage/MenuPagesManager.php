<?php
/**
 * Obtiene y valida las Menu Page y las Sub Menu Pages para su creación.
 * 
 */
namespace Fw\Init\MenuPage;

use Fw\Paths;
use Fw\Init\MenuPage\MenuPages;
use Fw\Core\Request\Request;

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
        
        $menuPages = [];
        foreach ($dirs as $directory) {
            $mainPath = Paths::buildPath($this->path, $directory);

            if ( !$files = glob(Paths::buildPath($mainPath, '*.php')) ) {
                continue;
            }

            # Menu Page Principal
            if ( $menuPageKey = array_search(Paths::buildPath($mainPath, "{$directory}.php"), $files) ) {
                $menuPage = $files[$menuPageKey];
                unset($files[$menuPageKey]);
            } else if ( $menuPageKey = array_search(Paths::buildPath($mainPath, "{$directory}Controller.php"), $files) ) {
                $menuPage = $files[$menuPageKey];
                unset($files[$menuPageKey]);
            } else {
                $menuPage = $files[0];
                unset($files[0]);
            }
            
            # Menu Page
            $menuPages[$directory] = $this->prepareMenuPage(
                Paths::buildNamespacePath(
                    $this->namespace, 
                    'Controllers', 
                    'MenuPages', 
                    $directory, 
                    basename($menuPage, '.php')
                )
            );

            # Sub Menu Page
            foreach ($files as $file) {
                $menuPages[$directory]['subMenuPages'][] = $this->prepareMenuPage(
                    Paths::buildNamespacePath(
                        $this->namespace, 
                        'Controllers', 
                        'MenuPages', 
                        $directory, 
                        basename($file, '.php')
                    ),
                    true
                );
            }
        }
        
        if ( count($menuPages) > 0 ) {
            $menuPages['path'] = Paths::findPluginPath($this->path);
            $this->args = $menuPages;
            # Se envian los argumentos para la creación de las (Sub) Menu Page.
            MenuPages::createMenuPages($this->args);
        }
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
        $menuSlug = explode('\\', $class);
        $menuPage['menuSlug'] = Request::propertyExists($class, 'menuSlug') ? $class::$menuSlug : $controllerName;
        $menuPage['menuSlug'] = strToSlug($menuSlug[0]) . '-' . strToSlug($menuSlug[3]) . '-' . strToSlug($menuPage['menuSlug']);

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

}
