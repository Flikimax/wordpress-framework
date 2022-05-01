<?php
/**
 * Shortcode
 * 
 */

# namespace;

class AlohaMundoController
{
    /**
     * Método defaul.
     * 
     * Ejemplo 1: 
     * Nombre del plugin: aloha-mundo
     * Shortcode: [carpeta_plugin Alohamundo=AlohaMundo]
     *            [tag                C.C=Controller]
     * 
     * Ejemplo 2:
     * Nombre del plugin: alohaMundo
     * Shortcode: [carpetaplugin Alohamundo=AlohaMundo]
     *            [tag               C.C=Controller]
     * 
     * C.C = Carpeta Contenedora.
     * 
     * @return Response
     */
    public function index()
    { 
        return view('layout', [
            'content' => view('index', [
                'title' => 'Shortcode Aloha Mundo',
            ]),
        ]);
    }

    /**
     * Método saludo.
     * 
     * Ejemplo 1: 
     * Nombre del plugin: aloha-mundo
     * Shortcode: [carpeta_plugin Alohamundo=AlohaMundo@saludo]
     *            [tag                C.C=Controller@method]
     * 
     * Ejemplo 2:
     * Nombre del plugin: alohaMundo
     * Shortcode: [carpetaplugin Alohamundo=AlohaMundo@saludo]
     *            [tag               C.C=Controller@method]
     * 
     * C.C = Carpeta Contenedora.
     * 
     * @return Response
     */
    public function saludo()
    {
        $currentUser = wp_get_current_user();
        return view('layout', [
            'content' => view('index', [
                'title' => "Aloha Mundo, {$currentUser->display_name}",
            ]),
        ]);
    }

}
