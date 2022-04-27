<?php
/**
 * Menu Page principal del Framework.
 * 
 */

namespace WordpressFramework\Controllers\MenuPages\WpFramework;

class WpFramework
{

    # Opcionales.
    # https://developer.wordpress.org/reference/functions/add_menu_page
    public static $pageTitle = 'WP Framework';
    public static $menuTitle = 'WP Framework';

    /**
     * Método por default.
     *
     **/
    public function index()
    { 
        # Si llegan datos del formulario.
        if ( $_POST['data'] ?? false ) {
            $this->createPlugin($_POST['data']);
        }

        return view('layout', [
            'content' => view('menuPages/frameworkWp/frameworkWp/index', [
                'title' => get_admin_page_title()
            ]),
        ]);
    }

    /**
     * Creación del plugin.
     *
     * @param array $data
     * @return void
     **/
    private function createPlugin(array $data) : void
    {
        if ( !wp_verify_nonce($_REQUEST['fwNonce'], 'create-plugin-nonce') ) {
            wp_die("Error: Invalid nonce check.");
        }
        
        echo "<h1>Create Component: {$data['pluginName']}.</h1>";
        
        if ( !\Fw\Structures\BuildStructures::basePlugin($data) ) {
            echo "<h3>Error en la creación del plugin.</h3>";

        } else {
            echo "<h3>Plugin creado con exito.</h3>";
        }
    }

    /**
     * Página de error 404.
     *
     **/
    public function error404()
    {
        return view('layout', [
            'content' => view('404', [
                'backText' => 'GO MENU PAGE',
                'backLink' => menu_page_url($_GET['page'], false)
            ]),
        ]);
    }

}
