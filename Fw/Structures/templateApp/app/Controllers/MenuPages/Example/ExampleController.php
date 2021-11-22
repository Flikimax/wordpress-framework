<?php
/**
 * Menu Page principal 
 * 
 */

# namespace

class ExampleController
{
    public function index()
    { 
        return view('layout', [
            'content' => view('index', [
                'title' => get_admin_page_title(),
            ]),
        ]);
    }

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
