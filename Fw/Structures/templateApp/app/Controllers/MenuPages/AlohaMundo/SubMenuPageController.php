<?php
/**
 * Sub Menu Page 
 * 
 */

# namespace

class SubMenuPageController
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
