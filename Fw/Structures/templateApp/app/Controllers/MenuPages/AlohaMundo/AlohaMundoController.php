<?php
/**
 * Menu Page principal 
 * 
 */

# namespace;

class AlohaMundoController
{
    /**
     * Método defaul.
     * 
     * @return Response
     */
    public function index()
    { 
        return view('layout', [
            'content' => view('index', [
                'title' => get_admin_page_title(),
            ]),
        ]);
    }

    /**
     * Método saludo.
     * Adicionar a la ruta default: &option=saludo
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

    /**
     * Método que se ejecuta cuando no se encuentra una ruta.
     * 
     * @return Response
     */
    public function error404()
    {
        return view('layout', [
            'content' => view('404', [
                'backText' => 'GO BACK',
                'backLink' => menu_page_url($_GET['page'], false)
            ]),
        ]);
    }

}
